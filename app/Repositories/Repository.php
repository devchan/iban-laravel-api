<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\DataTables;

class Repository implements RepositoryInterFace
{
    // model property on class instances
    protected $model;

    // Constructor to bind model to repo
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    // Get all instances of model
    public function all()
    {
        return $this->model->all();
    }

    // create a new record in the database
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    // update record in the database
    public function update(array $data, $id)
    {
        $record = $this->model->findOrFail($id);

        return $record->update($data);
    }

    // remove record from the database
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    // restore the record from the database
    public function restore($id)
    {
        return $this->model->withTrashed()->find($id)->restore();
    }

    // show the record with the given id
    public function show($id)
    {
        return $this->model->findOrFail($id);
    }

    // Get the associated model
    public function getModel()
    {
        return $this->model;
    }

    // Set the associated model
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    // Eager load database relationships
    public function with($relations)
    {
        return $this->model->with($relations);
    }

    public function addMedia($id, $file, $collection, $customProperties = [])
    {
        $collection = ! $collection ? 'default' : $collection;
        $model = $this->model->findOrFail($id);

        if ($file) {
            //$path = $this->moveFile($file);
            return $model->addMedia($file)
                ->withCustomProperties($customProperties)
                ->toMediaCollection($collection);
        }
    }

    protected function moveFile($file)
    {
        $name = $file->getClientOriginalName();

        $fileParts = pathinfo($name);
        $ext = $fileParts['extension'];
        $name = $fileParts['filename'];
        $fileNameWithHash = $this->createFileName('', $name, $ext);
        $destination = storage_path().'/media';

        $file->move($destination, $fileNameWithHash);

        return $destination.'/'.$fileNameWithHash;
    }

    public function clearMedia($id, $slug)
    {
        $model = $this->model->findOrFail($id);
        if ($model->getFirstMedia($slug)) {
            $model->media->each->delete($slug);
        }
    }

    private function createFileName($path, $fileName, $ext)
    {
        $fileName = $this->cleanFileName($fileName);
        $path = $path.$fileName;
        $hashKey = hash('ripemd160', $path.date('Y-m-d H:i:s'));

        return $fileName.'-'.$hashKey.'.'.$ext;
    }

    public function cleanFileName($fileName)
    {
        $fileName = strtolower(str_replace(' ', '-', $fileName)); // Replaces all spaces with hyphens.
        $fileName = preg_replace('/[^A-Za-z0-9\-]/', '', $fileName); // Removes special chars.

        return preg_replace('/-+/', '-', $fileName); // Replaces multiple hyphens with single one.
    }

    public function getDataTable($viewPath, $resource)
    {
        return DataTables::of($this->all())
            ->addColumn('action', function ($dataSet) use ($viewPath, $resource) {
                return view($viewPath, ['data' => $dataSet, 'resource' => $resource]);
            })->make(true);
    }
}
