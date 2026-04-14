<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryTranslationRequest;
use App\Http\Requests\UpdateCategoryTranslationRequest;
use App\Models\CategoryTranslation;

class CategoryTranslationController extends Controller
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
    public function store(StoreCategoryTranslationRequest $request): void
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CategoryTranslation $categoryTranslation): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CategoryTranslation $categoryTranslation): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryTranslationRequest $request, CategoryTranslation $categoryTranslation): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CategoryTranslation $categoryTranslation): void
    {
        //
    }
}
