<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreEntityContactRequest;
use App\Http\Requests\UpdateEntityContactRequest;
use App\Models\EntityContact;

class EntityContactController extends Controller
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
    public function store(StoreEntityContactRequest $request): void
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(EntityContact $entityContact): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EntityContact $entityContact): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEntityContactRequest $request, EntityContact $entityContact): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EntityContact $entityContact): void
    {
        //
    }
}
