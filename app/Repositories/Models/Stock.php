<?php namespace Owl\Repositories\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    const RANKING_STOCK_KEY = 'ranking_stock_';
    const EXPIRE_AT_ADD_MINUTES = 15;

    protected $fillable = array('user_id', 'item_id');

    public function user() {
        return $this->belongsTo('Owl\Repositories\Models\User');
    }
    public function item() {
        return $this->belongsTo('Owl\Repositories\Models\Item');
    }

    public static function getStockList($userId) {
        return \DB::table('stocks')
                    ->join('items', 'stocks.item_id', '=', 'items.id')
                    ->join('users', 'items.user_id', '=', 'users.id')
                    ->where('stocks.user_id', $userId)
                    ->select('users.email', 'users.username', 'items.open_item_id',
                            'items.updated_at', 'items.title')
                    ->orderBy('stocks.created_at', 'desc')
                    ->paginate(10);
    }

    public static function getRankingStockList($limit, $dayPeriod = null) {
        $options = array($limit);
        $where = '';
        if (!empty($dayPeriod)) {
            $where = <<<__SQL__
                where i.created_at > ?
__SQL__;
            $period = Carbon::now()->subDays($dayPeriod);
            array_unshift($options, $period);
        }
        $query = <<<__SQL__
            select 
                * 
            from 
                items as i 
                inner join ( 
                    select 
                        s.item_id, count(*) as stock_count 
                    from 
                        stocks s 
                    group by 
                        s.item_id 
                ) as sc on i.id = sc.item_id 
            $where
                and i.published = 2
            order by 
                sc.stock_count desc 
            limit ? 

__SQL__;
        return \DB::select($query,$options);
    }

    public static function getRankingWithCache($limit) {
        $result = \Cache::get(Stock::RANKING_STOCK_KEY.$limit);

        if (!empty($result)) return $result;

        $result = Stock::getRankingStockList($limit);
        $expiresAt = Carbon::now()->addMinutes(Stock::EXPIRE_AT_ADD_MINUTES);
        \Cache::put(Stock::RANKING_STOCK_KEY.$limit, $result, $expiresAt);

        return $result;
    }

    public static function getRecentRankingWithCache($limit, $dayPeriod) {
        $result = Stock::getRankingStockList($limit, $dayPeriod);
        return $result;
    }
}
