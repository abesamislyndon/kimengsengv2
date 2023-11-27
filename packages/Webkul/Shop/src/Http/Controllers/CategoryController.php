<?php

namespace Webkul\Shop\Http\Controllers;

use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Velocity\Helpers\Helper;

class CategoryController extends Controller
{
    protected $categoryRepository;
    protected $ProductRepository;
    protected $velocityHelper;

    public function __construct(CategoryRepository $categoryRepository, ProductRepository $productRepository, Helper $velocityHelper)
    {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->velocityHelper = $velocityHelper;
    }

    public function allCategories()
    {
        $categories = $this->categoryRepository->getVisibleCategoryTree();

        return view('shop::home.all-categories', compact('categories'));
    }

    // public function getCategoryProducts($categoryId)
    // {
    //     /* fetch category details */
    //     $categoryDetails = $this->categoryRepository->find($categoryId);

    //     /* if category not found then return empty response */
    //     if (!$categoryDetails) {
    //         return response()->json([
    //             'products' => [],
    //             'paginationHTML' => '',
    //         ]);
    //     }

    //     /* fetching products */
    //     $products = $this->productRepository->getAll($categoryId);
    //     $products->withPath($categoryDetails->slug);

    //     /* sending response */
    //     // return response()->json([
    //     //     'products' => collect($products->items())->map(function ($product) {
    //     //         return $this->velocityHelper->formatProduct($product);
    //     //     }),
    //     //     'paginationHTML' => $products->appends(request()->input())->links()->toHtml(),
    //     // ]);

    //     // return view('shop::home.category-products', [
    //     //     'products' => collect($products->items())->map(function ($product) {
    //     //         return $this->velocityHelper->formatProduct($product);
    //     //     }),
    //     //     'paginationHTML' => $products->appends(request()->input())->links()->toHtml(),
    //     //     'categoryId' => $categoryId,
    //     // ]);

    //     return view('shop::home.category-products', compact('products'));

    // }

    public function getCategoryProducts($categoryId)
    {
        /* fetch category details */
        $categoryDetails = $this->categoryRepository->find($categoryId);

        /* if category not found then return empty response */
        if (!$categoryDetails) {
            return view('shop::home.category-products', ['products' => []]);
        }

        /* fetching products */
        $products = $this->productRepository->getAll($categoryId);
        $products->withPath($categoryDetails->slug);

        /* format products if needed */
        $formattedProducts = $products->map(function ($product) {
            return $this->velocityHelper->formatProduct($product);
        });

        return view('shop::home.category-products', [
            'products' => $formattedProducts,
            'paginationHTML' => $products->appends(request()->input())->links()->toHtml(),
            'categoryId' => $categoryId,
        ]);
    }

}