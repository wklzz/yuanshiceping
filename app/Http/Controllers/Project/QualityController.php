<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Zack\Facades\MyAdmin as Admin;
use App\Zack\MyForm as Form;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Grid;
use App\Models\Quality;

class QualityController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header(trans('quality.title'));
            $content->description(trans('admin.list'));
            $content->body($this->grid()->render());
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {
            $content->header(trans('quality.title'));
            $content->description(trans('admin.create'));
            $content->body($this->form());
        });
    }


    /**
     * Edit interface.
     *
     * @param $id
     *
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header(trans('quality.title'));
            $content->description(trans('admin.edit'));
            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Quality::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->name(trans('quality.name'));
            $grid->status(trans('subject.status'))->display(function ($status_id) {
                return Subject::$STATUS[$status_id];
            });

            $grid->actions(function (Grid\Displayers\Actions $actions) {


            });

            $grid->tools(function (Grid\Tools $tools) {
                $tools->batch(function (Grid\Tools\BatchActions $actions) {
                    $actions->disableDelete();
                });
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {

        return Admin::form(Quality::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->text('name', trans('quality.name'))->rules('required');
            $form->text('sort', trans('subject.sort'));
            $form->radio('status', trans('subject.status'))->values(Subject::$STATUS)->default(Subject::STATUS_ON);

        });
    }
}
