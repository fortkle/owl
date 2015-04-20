<?php namespace Owl\Http\Controllers;

use Owl\Repositories\Models\Template;

class TemplateController extends Controller {

    public function create(){
        return \View::make('templates.create');
    }

    public function store(){
        $valid_rule = array(
            'display_title' => 'required|max:255',
            'title' => 'required|max:255',
            'tags' => 'alpha_comma|max:64',
            'body' => 'required',
        );
        $validator = \Validator::make(\Input::all(), $valid_rule);

        if ($validator->fails()) {
            return \Redirect::back()->withErrors($validator)->withInput();
        }

        $template = new Template;
        $template->fill(array(
            'display_title' => \Input::get('display_title'),
            'title' => \Input::get('title'),
            'tags' => \Input::get('tags'),
            'body' => \Input::get('body'),
        ));
        $template->save();
        return \Redirect::to('/templates'); 
    }

    public function index(){
        $templates = Template::orderBy('id', 'desc')->get();
        return \View::make('templates.index', compact('templates'));
    }

    public function show(){
        return \Redirect::to('/templates'); 
    }

    public function edit($templateId){
        $template = Template::where('id',$templateId)->first();
        if ($template == null){
            \App::abort(404);
        }
        return \View::make('templates.edit', compact('template'));
    }

    public function update($templateId){
        $valid_rule = array(
            'display_title' => 'required|max:255',
            'title' => 'required|max:255',
            'tags' => 'alpha_comma|max:64',
            'body' => 'required',
        );
        $validator = \Validator::make(\Input::all(), $valid_rule);

        if ($validator->fails()) {
            return \Redirect::back()->withErrors($validator)->withInput();
        }

        $template = Template::where('id',$templateId)->first();
        if ($template == null){
            \App::abort(404);
        }
        $template->fill(array(
            'display_title' => \Input::get('display_title'),
            'title' => \Input::get('title'),
            'tags' => \Input::get('tags'),
            'body' => htmlspecialchars(\Input::get('body'), ENT_QUOTES, 'UTF-8'),
        ));
        $template->save();
        return \Redirect::route('templates.index');
    }

    public function destroy($templateId){
        Template::where('id',$templateId)->delete();
        return \Redirect::route('templates.index');
    }
}
