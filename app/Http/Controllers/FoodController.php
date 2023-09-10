<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Food;
use Illuminate\Http\Request;

class FoodController extends Controller
{

    public function listFoods()
    {
        $categories = Category::with('food')->get();
        return view('welcome', compact('categories'));
    }

    public function viewFoods(string $id)
    {
        $food = Food::find($id);
        return view('food.view', compact('food'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $foods = Food::all();
        return view('food.index', compact('foods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('food.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'category' => 'required',
            'image' => 'required',
        ]);

        $image = $request->file('image');
        $name = time(). '.' . $image->getClientOriginalExtension();
        $image_path = public_path('/images');
        $image->move($image_path, $name);

        Food::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category,
            'image' => $name,
        ]);

        return redirect()->route('food.index')->with('message', 'Food item created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $food = Food::find($id);
        return view('food.edit', compact('food'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'category' => 'required',
            'image' => 'required',
        ]);

        $food = Food::find($id);
        $image = $food->image;
        $old_image = $request->old_image;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name = time(). '.' . $image->getClientOriginalExtension();
            $image_path = public_path('/images');
            $image->move($image_path, $name);

            unlink(public_path('/images/' . $old_image));

        }

        $food->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category,
            'image' => $name,
        ]);

        return redirect()->route('food.index')->with('message', 'Food item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $food = Food::find($id);
        $food->delete();
        unlink(public_path('/images/' . $food->image));

        return redirect()->route('food.index')->with('message', 'Food item deleted successfully.');
    }
}
