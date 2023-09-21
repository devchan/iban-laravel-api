<?php

namespace App\Repositories;

interface RepositoryInterFace
{
    public function all();

    public function create(array $data);

    public function update(array $data, $id);

    public function delete($id);

    public function show($id);

    //Yajra\DataTables\DataTables
    public function getDataTable($viewPath, $resource);
}
