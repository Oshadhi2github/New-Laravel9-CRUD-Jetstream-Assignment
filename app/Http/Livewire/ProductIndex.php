<?php

namespace App\Http\Livewire;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductIndex extends Component
{
    use WithFileUploads;

    public $showingProductModal = false;

    public $title;
    public $newImage;
    public $body;
    public $oldImage;
    public $isEditMode = false;
    public $product;

    public function showProductModal()
    {
        $this->reset();
        $this->showingProductModal = true;
    }

    public function storeProduct()
    {
        $this->validate([
            'newImage' => 'image|max:1024', // 1MB Max
            'title' => 'required',
            'body' => 'required'
        ]);

        $image = $this->newImage->store('public/products');
        Product::create([
            'title' => $this->title,
            'image' => $image,
            'body' => $this->body,
        ]);
        $this->reset();
    }

    public function showEditProductModal($id)
    {
        $this->product = Product::findOrFail($id);
        $this->title = $this->product->title;
        $this->body = $this->product->body;
        $this->oldImage = $this->product->image;
        $this->isEditMode = true;
        $this->showingProductModal = true;
    }

    public function updateProduct()
    {
        $this->validate([

            'title' => 'required',
            'body' => 'required'
        ]);
        $image = $this->product->image;
        if($this->newImage){
            $image = $this->newImage->store('public/products');
        }
        $this->product->update([
            'title' => $this->title,
            'image' => $image,
            'body' => $this->body
        ]);
        $this->reset();
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        Storage::delete($product->image);
        $product->delete();
        $this->reset();
    }
    public function render()
    {
        return view('livewire.product-index', [
            'products' =>Product::all()
        ]);
    }
}
