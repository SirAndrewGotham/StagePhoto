<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreAlbumRequest;
use App\Http\Requests\UpdateAlbumRequest;
use App\Models\Album;

class AlbumController extends Controller
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
    public function store(StoreAlbumRequest $request): void
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Album $album): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Album $album): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAlbumRequest $request, Album $album): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Album $album): void
    {
        //
    }
}
