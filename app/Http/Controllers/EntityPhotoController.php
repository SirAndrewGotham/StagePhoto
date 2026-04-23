<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreEntityPhotoRequest;
use App\Http\Requests\UpdateEntityPhotoRequest;
use App\Models\EntityPhoto;

class EntityPhotoController extends Controller
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
    public function store(StoreEntityPhotoRequest $request): void
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(EntityPhoto $entityPhoto): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EntityPhoto $entityPhoto): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEntityPhotoRequest $request, EntityPhoto $entityPhoto): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EntityPhoto $entityPhoto): void
    {
        //
    }
}
