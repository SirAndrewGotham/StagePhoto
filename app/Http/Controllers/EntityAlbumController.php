<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreEntityAlbumRequest;
use App\Http\Requests\UpdateEntityAlbumRequest;
use App\Models\EntityAlbum;

class EntityAlbumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): void
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): void
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEntityAlbumRequest $request): void
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(EntityAlbum $entityAlbum): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EntityAlbum $entityAlbum): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEntityAlbumRequest $request, EntityAlbum $entityAlbum): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EntityAlbum $entityAlbum): void
    {
        //
    }
}
