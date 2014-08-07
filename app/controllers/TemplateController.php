<?php

class TemplateController extends BaseController{

    public function create(){
        return View::make('templates.create');
    }

    public function store(){
        $template = new Template;
        $template->fill(array(
            'display_title'=>Input::get('display_title'),
            'title'=>Input::get('title'),
            'body'=>Input::get('body'),
        ));
        $template->save();
        return Redirect::to('/templates'); 
    }

    public function index(){
        $templates = Template::orderBy('id', 'desc')->get();
        return View::make('templates.index', compact('templates'));
    }

    public function show(){
        return Redirect::to('/templates'); 
    }

    public function edit($templateId){
        $template = Template::where('id',$templateId)->first();
        if ($template == null){
            App::abort(404);
        }
        return View::make('templates.edit', compact('template'));
    }

    public function update($templateId){
        $template = Template::where('id',$templateId)->first();
        if ($template == null){
            App::abort(404);
        }
        $template->fill(array(
            'display_title'=>Input::get('display_title'),
            'title'=>Input::get('title'),
            'body'=>htmlspecialchars(Input::get('body'), ENT_QUOTES, 'UTF-8'),
        ));
        $template->save();
        return Redirect::route('templates.index');
    }

    public function destroy($templateId){
        Template::where('id',$templateId)->delete();
        return Redirect::route('templates.index');
    }
}
