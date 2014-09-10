<?php

class TodosController extends BaseController
{
    protected $viewBase = 'todos';

    public function __construct()
    {
        $this->beforeFilter('@authorize', ['except' => 'index']);
    }

    public function index()
    {
        return Response::json(Todo::withUsers()->get());
    }

    public function store()
    {
        $form = new TodoCreation($this->currentUser(), $this->params());

        if ($form->save()) {
            return Response::json($form->getTodo(), 201);
        } else {
            return Response::json($form->getErrors(), 409);
        }
    }

    public function update(Todo $todo)
    {
        $form = new TodoChange($this->currentUser(), $todo, $this->params());

        if ($form->save()) {
            return Response::json($form->getTodo(), 200);
        } else {
            return Response::json($form->getErrors(), 409);
        }
    }

    public function destroy(Todo $todo)
    {
        $todo->delete();
        return Response::json(null, 204);
    }

    protected function params()
    {
        return Input::only('name', 'completed');
    }

    protected function authorize($route, $request)
    {
        $allowed = App::make('canI')->can('manage', $route->parameter('todos'));

        if (! $allowed) {
            return App::abort(401);
        }
    }
}
