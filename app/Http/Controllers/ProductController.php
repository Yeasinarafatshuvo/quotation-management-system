<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductTranslation;
use App\Models\ProductVariation;
use App\Models\ProductVariationCombination;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\UserHistory;
use App\Models\ProductTax;
use App\Models\ShopBrand;
use App\Models\Wastage;
use App\Models\User;
use App\Models\Setting;
use App\Utility\CategoryUtility;
use App\Http\Controllers\CategoryController;
use Artisan;
use Auth;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:show_products'])->only('index');
        $this->middleware(['permission:add_products'])->only('create');
        $this->middleware(['permission:view_products'])->only('show');
        $this->middleware(['permission:edit_products'])->only('edit');
        $this->middleware(['permission:duplicate_products'])->only('duplicate');
        $this->middleware(['permission:delete_products'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $col_name = null;
        $query = null;
        $sort_search = null;
        $products = Product::orderBy('created_at', 'desc')->where('shop_id', auth()->user()->shop_id);
        if ($request->search != null) {
            $products = $products->where('name', 'like', '%' . $request->search . '%');
            $sort_search = $request->search;
        }
        if ($request->type != null) {
            $var = explode(",", $request->type);
            $col_name = $var[0];
            $query = $var[1];
            $products = $products->orderBy($col_name, $query);
            $sort_type = $request->type;
        }

        $products = $products->paginate(15);
        $type = 'All';

        return view('backend.product.products.index', compact('products', 'type', 'col_name', 'query', 'sort_search'));
    }


    public function product_search($search_item){
        if($search_item == 'codered'){
            
            $user_id = Auth::id();
            if($user_id == "1"){
                $products = Product::orderBy('created_at', 'desc');
            }else{
                $products = Product::orderBy('created_at', 'desc')->where('shop_id', auth()->user()->shop_id);
            }
            $products = $products->paginate(15);
            $type = 'All';
            return view('product_index', compact('products','type'));
        }else{
            $user_id = Auth::id();
            if($user_id == "1"){
                $products = Product::orderBy('created_at', 'desc')->where('name','LIKE','%'.$search_item."%")->orwhereRaw('json_contains(random_search, \'["' . $search_item . '"]\')');
            }else{
                $products = Product::orderBy('created_at', 'desc')->where('shop_id', auth()->user()->shop_id)->where('name','LIKE','%'.$search_item."%")->orwhereRaw('json_contains(random_search, \'["' . $search_item . '"]\')');
            }

            $products = $products->paginate(15);
            $type = 'All';
            return view('product_index', compact('products','type'));
        }

}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::where('level', 0)->get();
        $attributes = Attribute::get();
        return view('backend.product.products.create', compact('categories', 'attributes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->has('is_variant') && !$request->has('variations')) {
            flash(translate('Invalid product variations'))->error();
            return redirect()->back();
        }
        
        

        $product                    = new Product;
        $product->name              = $request->name;
        $product->shop_id           = auth()->user()->shop_id;
        $product->brand_id          = $request->brand_id;
        $product->unit              = $request->unit;
        $product->min_qty           = $request->min_qty;
        $product->max_qty           = $request->max_qty;
        $product->photos            = $request->photos;
        $product->thumbnail_img     = $request->thumbnail_img;
        $product->description       = $request->description;
        $product->published         = $request->status;
        $product->product_warranty  = $request->product_warranty;
        // SEO meta
        $product->meta_title        = (!is_null($request->meta_title)) ? $request->meta_title : $product->name;
        $product->meta_description  = (!is_null($request->meta_description)) ? $request->meta_description : strip_tags($product->description);
        $product->meta_image          = (!is_null($request->meta_image)) ? $request->meta_image : $product->thumbnail_img;
        $product->slug              = Str::slug($request->name, '-') . '-' . strtolower(Str::random(5));

        // warranty
        $product->has_warranty      = $request->has('has_warranty') && $request->has_warranty == 'on' ? 1 : 0;

        // tag
        $tags                       = array();
        if ($request->tags != null) {
            foreach (json_decode($request->tags) as $key => $tag) {
                array_push($tags, $tag->value);
            }
        }
        $product->tags              = implode(',', $tags);

        // lowest highest price
        if ($request->has('is_variant') && $request->has('variations')) {
            $product->lowest_price  =  min(array_column($request->variations, 'price'));
            $product->highest_price =  max(array_column($request->variations, 'price'));
        } else {
            $product->lowest_price  =  $request->price;
            $product->highest_price =  $request->price;
        }
        $product->dealer_price =  $request->dealer_price;
        $product->corporate_price =  $request->corporate_price;
        $product->purchase_price =  $request->purchase_price;
        $product->stock_quantity =  $request->stock_quantity;
        $product->key_feature =  $request->key_feature;
        $product->random_search =  json_encode(explode(",",$request->random_search));
        // stock based on all variations
        $product->stock             = ($request->has('is_variant') && $request->has('variations')) ? max(array_column($request->variations, 'stock')) : $request->stock;

        // discount
        $product->discount          = $request->discount;
        $product->discount_type     = $request->discount_type;
        if ($request->date_range != null) {
            $date_var               = explode(" to ", $request->date_range);
            $product->discount_start_date = strtotime($date_var[0]);
            $product->discount_end_date   = strtotime($date_var[1]);
        }

        // shipping info
        $product->standard_delivery_time    = $request->standard_delivery_time;
        $product->express_delivery_time     = $request->express_delivery_time;
        $product->weight                    = $request->weight;
        $product->height                    = $request->height;
        $product->length                    = $request->length;
        $product->width                     = $request->width;

        $product->save();

        // Product Translations
        $product_translation = ProductTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'product_id' => $product->id]);
        $product_translation->name = $request->name;
        $product_translation->unit = $request->unit;
        $product_translation->description = $request->description;
        $product_translation->save();

        // category
        $product->categories()->sync($request->category_ids);

        // shop category ids
        $shop_category_ids = [];
        foreach ($request->category_ids ?? [] as $id) {
            $shop_category_ids[] = CategoryUtility::get_grand_parent_id($id);
        }
        $shop_category_ids =  array_merge(array_filter($shop_category_ids), $product->shop->shop_categories->pluck('category_id')->toArray());
        $product->shop->categories()->sync($shop_category_ids);

        // shop brand
        if ($request->brand_id) {
            ShopBrand::updateOrCreate([
                'shop_id' => $product->shop_id,
                'brand_id' => $request->brand_id,
            ]);
        }


        //taxes
        $tax_data = array();
        $tax_ids = array();
        if ($request->has('taxes')) {
            foreach ($request->taxes as $key => $tax) {
                array_push($tax_data, [
                    'tax' => $tax,
                    'tax_type' => $request->tax_types[$key]
                ]);
            }
            $tax_ids = $request->tax_ids;
        }
        $taxes = array_combine($tax_ids, $tax_data);

        $product->product_taxes()->sync($taxes);


        //product variation
        $product->is_variant        = ($request->has('is_variant') && $request->has('variations')) ? 1 : 0;

        if ($request->has('is_variant') && $request->has('variations')) {
            foreach ($request->variations as $variation) {
                $p_variation              = new ProductVariation;
                $p_variation->product_id  = $product->id;
                $p_variation->code        = $variation['code'];
                $p_variation->price       = $variation['price'];
                $p_variation->stock       = $variation['stock'];
                $p_variation->sku         = $variation['sku'];
                $p_variation->img         = $variation['img'];
                $p_variation->save();

                foreach (array_filter(explode("/", $variation['code'])) as $combination) {
                    $p_variation_comb                         = new ProductVariationCombination;
                    $p_variation_comb->product_id             = $product->id;
                    $p_variation_comb->product_variation_id   = $p_variation->id;
                    $p_variation_comb->attribute_id           = explode(":", $combination)[0];
                    $p_variation_comb->attribute_value_id     = explode(":", $combination)[1];
                    $p_variation_comb->save();
                }
            }
        } else {
            $variation              = new ProductVariation;
            $variation->product_id  = $product->id;
            $variation->sku         = $request->sku;
            $variation->price       = $request->price;
            $variation->stock       = $request->stock;
            $variation->save();
        }

        // attribute
        if ($request->has('product_attributes') && $request->product_attributes[0] != null) {
            foreach ($request->product_attributes as $attr_id) {
                $attribute_values = 'attribute_' . $attr_id . '_values';
                if ($request->has($attribute_values) && $request->$attribute_values != null) {
                    $p_attribute = new ProductAttribute;
                    $p_attribute->product_id = $product->id;
                    $p_attribute->attribute_id = $attr_id;
                    $p_attribute->save();

                    foreach ($request->$attribute_values as $val_id) {
                        $p_attr_value = new ProductAttributeValue;
                        $p_attr_value->product_id = $product->id;
                        $p_attr_value->attribute_id = $attr_id;
                        $p_attr_value->attribute_value_id = $val_id;
                        $p_attr_value->save();
                    }
                }
            }
        }


        $save_product = $product->save();

        //start add product  user history 
        if($save_product)
        {
            $user_history_instance = new UserHistory();
            $user_history_instance->user_id = Auth::id();
            $user_history_instance->user_action = "Add Product";
            $user_history_instance->prodcut_id = $product->id;
            $save_user_history = $user_history_instance->save();
        }
       
        //user add proudct history end

        flash(translate('Product has been inserted successfully'))->success();
        return redirect()->route('product.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('backend.product.products.show', [
            'product' => Product::withCount('reviews', 'wishlists', 'carts')->with('variations.combinations')->findOrFail($id)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $user_id = Auth::id();
        if($user_id == "1"){
            
        }
        elseif($product->shop_id != auth()->user()->shop_id) {
            abort(403);
        }

        $lang = $request->lang;
        $categories = Category::where('level', 0)->get();
        $all_attributes = Attribute::get();
        return view('backend.product.products.edit', compact('product', 'categories', 'lang', 'all_attributes'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->has('is_variant') && !$request->has('variations')) {
            flash(translate('Invalid product variations'))->error();
            return redirect()->back();
        }

        //user edit history start
        $ptoduct_all_data = Product::where('id', $request->id)->first();
        $edit_product_history = array();
           
        if($ptoduct_all_data->name !=  $request->name)
        {
            $edit_product_history['product_name'] = $request->name;
            
        }
        if($ptoduct_all_data->unit !=  $request->unit)
        {
            $edit_product_history['product_unit'] = $request->unit;
            
        }
        if($ptoduct_all_data->min_qty !=  $request->min_qty)
        {
            $edit_product_history['product_min_qty'] = $request->min_qty;
            
        }
        if($ptoduct_all_data->max_qty !=  $request->max_qty)
        {
            $edit_product_history['product_max_qty'] = $request->max_qty;
            
        }
        if($ptoduct_all_data->purchase_price !=  $request->purchase_price)
        {
            $edit_product_history['product_purchase_price'] = $request->purchase_price;
            
        }
        if($ptoduct_all_data->highest_price !=  $request->price)
        {
            $edit_product_history['product_highest_price'] = $request->price;
            
        }
        if($ptoduct_all_data->dealer_price !=  $request->dealer_price)
        {
            $edit_product_history['product_dealer_price'] = $request->dealer_price;
            
        }
        if($ptoduct_all_data->corporate_price !=  $request->corporate_price)
        {
            $edit_product_history['product_corporate_price'] = $request->corporate_price;
            
        }
        $product_variation_sku = ProductVariation::select('sku')->where('product_id', $request->id)->first();
       
        if($product_variation_sku->sku !=  $request->sku)
        {
            $edit_product_history['product_sku'] = $request->sku;
            
        }
        if($ptoduct_all_data->stock !=  $request->stock)
        {
            $edit_product_history['product_stock'] = $request->stock;
            
        }
        if($ptoduct_all_data->stock_quantity !=  $request->stock_quantity)
        {
            $edit_product_history['product_stock_quantity'] = $request->stock_quantity;
            
        }
        if($ptoduct_all_data->discount !=  $request->discount)
        {
            $edit_product_history['product_discount'] = $request->discount;
            
        }
        if($ptoduct_all_data->discount_type !=  $request->discount_type)
        {
            $edit_product_history['product_discount_type'] = $request->discount_type;
            
        }
        if($ptoduct_all_data->standard_delivery_time !=  $request->standard_delivery_time)
        {
            $edit_product_history['product_standard_delivery_time'] = $request->standard_delivery_time;
            
        }
        if($ptoduct_all_data->express_delivery_time !=  $request->express_delivery_time)
        {
            $edit_product_history['product_express_delivery_time'] = $request->express_delivery_time;
            
        }
        if($ptoduct_all_data->weight !=  $request->weight)
        {
            $edit_product_history['product_weight'] = $request->weight;
            
        }
        if($ptoduct_all_data->height !=  $request->height)
        {
            $edit_product_history['product_height'] = $request->height;
            
        }
        if($ptoduct_all_data->length !=  $request->length)
        {
            $edit_product_history['product_length'] = $request->length;
            
        }
        if($ptoduct_all_data->width !=  $request->width)
        {
            $edit_product_history['product_width'] = $request->width;
            
        }

        if($ptoduct_all_data->description !=  $request->description)
        {
            $edit_product_history['product_description'] = $request->description;
            
        }
        if($ptoduct_all_data->key_feature !=  $request->key_feature)
        {
            $edit_product_history['product_key_feature'] = $request->key_feature;
            
        }
        if(!empty($request->random_search)){
            $previous_random_search = implode(",",json_decode($ptoduct_all_data->random_search));
            if($previous_random_search !=  $request->random_search)
            {
                $edit_product_history['random_search'] = $request->random_search;
            }
        }
          
        if($ptoduct_all_data->meta_title !=  $request->meta_title)
        {
            $edit_product_history['product_meta_title'] = $request->meta_title;
            
        }
        if($ptoduct_all_data->meta_description !=  $request->meta_description)
        {
            $edit_product_history['product_meta_description'] = $request->meta_description;
            
        }
        if($ptoduct_all_data->slug !=  $request->slug)
        {
            $edit_product_history['product_slug'] = $request->slug;
            
        }

        if($ptoduct_all_data->published !=  $request->status)
        {
            $edit_product_history['product_published_status'] = $request->status;
            
        }
        if($ptoduct_all_data->brand_id !=  $request->brand_id)
        {
            $edit_product_history['product_brand_id'] = $request->brand_id;
            
        }
        // return $ptoduct_all_data->tags;
        $edited_products_tag_string = [];
        if(!empty($request->tags)){
            $edited_products_tag =  json_decode($request->tags);
            for($i = 0; $i < count($edited_products_tag); $i++){
                array_push($edited_products_tag_string, $edited_products_tag[$i]->value);
            }
        }
        
        if($ptoduct_all_data->tags !== implode(",",$edited_products_tag_string))
        {
            $edit_product_history['product_tags'] = $edited_products_tag_string;
        }
        
        $previous_categories =   json_decode(ProductCategory::select('category_id')->where('product_id',$request->id)->orderBy('category_id','asc')->get());
        $previous_categories_to_string = [];
        for($i = 0; $i <count($previous_categories); $i++)
        {
            array_push($previous_categories_to_string,$previous_categories[$i]->category_id);
        }
     
        if(!empty($request->category_ids)){
            if(implode(",",$previous_categories_to_string) !=  implode(",",$this->sort_numbers($request->category_ids)))
            {
                $edit_product_history['product_category'] = $request->category_ids;
                
            }
        }
       
        //need to check in server photos and thumbnail working or not
        if($request->photos != $ptoduct_all_data->photos)
        {
            $edit_product_history['product_photos'] = $request->photos;
        }
        if($request->thumbnail_img != $ptoduct_all_data->thumbnail_img)
        {
            $edit_product_history['product_thumbnail_img'] = $request->thumbnail_img;
        }

        if($request->product_warranty != $ptoduct_all_data->product_warranty)
        {
            $edit_product_history['product_warranty'] = $request->product_warranty;
        }



        if(count($edit_product_history) >= 1)
        {
            $user_history_instance = new UserHistory();
            $user_history_instance->user_id = Auth::id();
            $user_history_instance->user_action = "product update";
            $user_history_instance->prodcut_id = $request->id;
            $user_history_instance->change_information = json_encode($edit_product_history);
            $save_user_history = $user_history_instance->save();
        }
        //user edit history end

       

        $product                    = Product::findOrFail($id);
        $oldProduct                 = clone $product;

        if ($product->shop_id != auth()->user()->shop_id) {
            abort(403);
        }

        if ($request->lang == env("DEFAULT_LANGUAGE")) {
            $product->name          = $request->name;
            $product->unit          = $request->unit;
            $product->description   = $request->description;
        }

        $product->brand_id          = $request->brand_id;
        $product->min_qty           = $request->min_qty;
        $product->max_qty           = $request->max_qty;
        $product->photos            = $request->photos;
        $product->thumbnail_img     = $request->thumbnail_img;
        $product->published         = $request->status;
        $product->product_warranty  = $request->product_warranty;
        // Product Translations
        $product_translation                = ProductTranslation::firstOrNew(['lang' => $request->lang, 'product_id' => $product->id]);
        $product_translation->name          = $request->name;
        $product_translation->unit          = $request->unit;
        $product_translation->description   = $request->description;
        $product_translation->save();


        // SEO meta
        $product->meta_title        = (!is_null($request->meta_title)) ? $request->meta_title : $product->name;
        $product->meta_description  = (!is_null($request->meta_description)) ? $request->meta_description : strip_tags($product->description);
        $product->meta_image        = (!is_null($request->meta_image)) ? $request->meta_image : $product->thumbnail_img;
        $product->slug              = (!is_null($request->slug)) ? Str::slug($request->slug, '-') : Str::slug($request->name, '-') . '-' . strtolower(Str::random(5));

        // warranty
        $product->has_warranty      = $request->has('has_warranty') && $request->has_warranty == 'on' ? 1 : 0;


        // tag
        $tags                       = array();
        if ($request->tags != null) {
            foreach (json_decode($request->tags) as $key => $tag) {
                array_push($tags, $tag->value);
            }
        }
        $product->tags              = implode(',', $tags);

        // lowest highest price
        if ($request->has('is_variant') && $request->has('variations')) {
            $product->lowest_price  =  min(array_column($request->variations, 'price'));
            $product->highest_price =  max(array_column($request->variations, 'price'));
        } else {
            $product->lowest_price  =  $request->price;
            $product->highest_price =  $request->price;
        }
        if ($request->has('dealer_price')){
            $product->dealer_price =  $request->dealer_price;
        }
        if ($request->has('corporate_price')){
            $product->corporate_price =  $request->corporate_price;
        }
        if ($request->has('purchase_price')){
            $product->purchase_price =  $request->purchase_price;
        }
        $product->stock_quantity =  $request->stock_quantity;
        $product->key_feature =  $request->key_feature;
        $product->random_search =  json_encode(explode(",",$request->random_search));
        // stock based on all variations
        $product->stock             = ($request->has('is_variant') && $request->has('variations')) ? max(array_column($request->variations, 'stock')) : $request->stock;

        // discount
        $product->discount          = $request->discount;
        $product->discount_type     = $request->discount_type;
        if ($request->date_range != null) {
            $date_var               = explode(" to ", $request->date_range);
            $product->discount_start_date = strtotime($date_var[0]);
            $product->discount_end_date   = strtotime($date_var[1]);
        }

        // shipping info
        $product->standard_delivery_time    = $request->standard_delivery_time;
        $product->express_delivery_time     = $request->express_delivery_time;
        $product->weight                    = $request->weight;
        $product->height                    = $request->height;
        $product->length                    = $request->length;
        $product->width                     = $request->width;

        // category
        $product->categories()->sync($request->category_ids);

        // shop category ids
        $shop_category_ids = [];
        foreach ($request->category_ids ?? [] as $id) {
            $shop_category_ids[] = CategoryUtility::get_grand_parent_id($id);
        }
        $shop_category_ids =  array_merge(array_filter($shop_category_ids), $product->shop->shop_categories->pluck('category_id')->toArray());
        $product->shop->categories()->sync($shop_category_ids);

        // shop brand
        if ($request->brand_id) {
            ShopBrand::updateOrCreate([
                'shop_id' => $product->shop_id,
                'brand_id' => $request->brand_id,
            ]);
        }

        // taxes
        $tax_data = array();
        $tax_ids = array();
        if ($request->has('taxes')) {
            foreach ($request->taxes as $key => $tax) {
                array_push($tax_data, [
                    'tax' => $tax,
                    'tax_type' => $request->tax_types[$key]
                ]);
            }
            $tax_ids = $request->tax_ids;
        }
        $taxes = array_combine($tax_ids, $tax_data);

        $product->product_taxes()->sync($taxes);


        //product variation
        $product->is_variant        = ($request->has('is_variant') && $request->has('variations')) ? 1 : 0;

        if ($request->has('is_variant') && $request->has('variations')) {

            $requested_variations = collect($request->variations);
            $requested_variations_code = $requested_variations->pluck('code')->toArray();
            $old_variations_codes = $product->variations->pluck('code')->toArray();
            $old_matched_variations = $requested_variations->whereIn('code', $old_variations_codes);
            $new_variations = $requested_variations->whereNotIn('code', $old_variations_codes);


            // delete old variations that didn't requested
            $product->variations->whereNotIn('code', $requested_variations_code)->each(function ($variation) {
                foreach ($variation->combinations as $comb) {
                    $comb->delete();
                }
                $variation->delete();
            });

            // update old matched variations
            foreach ($old_matched_variations as $variation) {
                $p_variation              = ProductVariation::where('product_id', $product->id)->where('code', $variation['code'])->first();
                $p_variation->price       = $variation['price'];
                $p_variation->stock       = $variation['stock'];
                $p_variation->sku         = $variation['sku'];
                $p_variation->img         = $variation['img'];
                $p_variation->save();
            }


            // insert new requested variations
            foreach ($new_variations as $variation) {
                $p_variation              = new ProductVariation;
                $p_variation->product_id  = $product->id;
                $p_variation->code        = $variation['code'];
                $p_variation->price       = $variation['price'];
                $p_variation->stock       = $variation['stock'];
                $p_variation->sku         = $variation['sku'];
                $p_variation->img         = $variation['img'];
                $p_variation->save();

                foreach (array_filter(explode("/", $variation['code'])) as $combination) {
                    $p_variation_comb                         = new ProductVariationCombination;
                    $p_variation_comb->product_id             = $product->id;
                    $p_variation_comb->product_variation_id   = $p_variation->id;
                    $p_variation_comb->attribute_id           = explode(":", $combination)[0];
                    $p_variation_comb->attribute_value_id     = explode(":", $combination)[1];
                    $p_variation_comb->save();
                }
            }
        } else {
            // check if old product is variant then delete all old variation & combinations
            if ($oldProduct->is_variant) {
                foreach ($product->variations as $variation) {
                    foreach ($variation->combinations as $comb) {
                        $comb->delete();
                    }
                    $variation->delete();
                }
            }

            $variation              = $product->variations->first();
            $variation->product_id  = $product->id;
            $variation->code        = null;
            $variation->sku         = $request->sku;
            $variation->price       = $request->price;
            $variation->stock       = $request->stock;
            $variation->save();
        }


        // attributes + values
        foreach ($product->attributes as $attr) {
            $attr->delete();
        }
        foreach ($product->attribute_values as $attr_val) {
            $attr_val->delete();
        }
        if ($request->has('product_attributes') && $request->product_attributes[0] != null) {
            foreach ($request->product_attributes as $attr_id) {
                $attribute_values = 'attribute_' . $attr_id . '_values';
                if ($request->has($attribute_values) && $request->$attribute_values != null) {
                    $p_attribute = new ProductAttribute;
                    $p_attribute->product_id = $product->id;
                    $p_attribute->attribute_id = $attr_id;
                    $p_attribute->save();

                    foreach ($request->$attribute_values as $val_id) {
                        $p_attr_value = new ProductAttributeValue;
                        $p_attr_value->product_id = $product->id;
                        $p_attr_value->attribute_id = $attr_id;
                        $p_attr_value->attribute_value_id = $val_id;
                        $p_attr_value->save();
                    }
                }
            }
        }
       
            
        $save_product = $product->save();



        flash(translate('Product has been updated successfully'))->success();
        return redirect()->route('product.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->product_translations()->delete();
        $product->variations()->delete();
        $product->variation_combinations()->delete();
        $product->reviews()->delete();
        $product->product_categories()->delete();
        $product->carts()->delete();
        $product->offers()->delete();
        $product->wishlists()->delete();
        $product->attributes()->delete();
        $product->attribute_values()->delete();
        $product->taxes()->delete();

        if (Product::destroy($id)) {
            
            //start add product  user history 
            $user_history_instance = new UserHistory();
            $user_history_instance->user_id = Auth::id();
            $user_history_instance->user_action = "Delete Product";
            $user_history_instance->prodcut_id = $id;
            $save_user_history = $user_history_instance->save();
            //user add proudct history end

            flash(translate('Product has been deleted successfully'))->success();
            return redirect()->route('product.index');
        } else {
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    /**
     * Duplicates the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function duplicate(Request $request, $id)
    {
        $product = Product::find($id);
        $product_new = $product->replicate();
        $product_new->slug = Str::slug($product_new->name, '-') . '-' . strtolower(Str::random(5));

        if ($product_new->save()) {

            // variation duplicate
            foreach ($product->variations as $key => $variation) {
                $p_variation              = new ProductVariation;
                $p_variation->product_id  = $product_new->id;
                $p_variation->code        = $variation->code;
                $p_variation->price       = $variation->price;
                $p_variation->stock       = $variation->stock;
                $p_variation->sku         = $variation->sku;
                $p_variation->img         = $variation->img;
                $p_variation->save();

                // variation combination duplicate
                foreach ($variation->combinations as $key => $combination) {
                    $p_variation_comb                         = new ProductVariationCombination;
                    $p_variation_comb->product_id             = $product_new->id;
                    $p_variation_comb->product_variation_id   = $p_variation->id;
                    $p_variation_comb->attribute_id           = $combination->attribute_id;
                    $p_variation_comb->attribute_value_id     = $combination->attribute_value_id;
                    $p_variation_comb->save();
                }
            }

            // attribute duplicate
            foreach ($product->attributes as $key => $attribute) {
                $p_attribute                = new ProductAttribute;
                $p_attribute->product_id    = $product_new->id;
                $p_attribute->attribute_id  = $attribute->attribute_id;
                $p_attribute->save();
            }

            // attribute value duplicate
            foreach ($product->attribute_values as $key => $attribute_value) {
                $p_attr_value                       = new ProductAttributeValue;
                $p_attr_value->product_id           = $product_new->id;
                $p_attr_value->attribute_id         = $attribute_value->attribute_id;
                $p_attr_value->attribute_value_id   = $attribute_value->attribute_value_id;
                $p_attr_value->save();
            }

            // translation duplicate
            foreach ($product->product_translations as $key => $translation) {
                $product_translation                = new ProductTranslation;
                $product_translation->product_id    = $product_new->id;
                $product_translation->name          = $translation->name;
                $product_translation->unit          = $translation->unit;
                $product_translation->description   = $translation->description;
                $product_translation->lang          = $translation->lang;
                $product_translation->save();
            }

            //categories duplicate
            foreach ($product->product_categories as $key => $category) {
                $p_category                 = new ProductCategory;
                $p_category->product_id     = $product_new->id;
                $p_category->category_id    = $category->category_id;
                $p_category->save();
            }

            // taxes duplicate
            foreach ($product->taxes as $key => $tax) {
                $p_tax                = new ProductTax;
                $p_tax->product_id    = $product_new->id;
                $p_tax->tax_id        = $tax->tax_id;
                $p_tax->tax           = $tax->tax;
                $p_tax->tax_type      = $tax->tax_type;
                $p_tax->save();
            }

            flash(translate('Product has been duplicated successfully'))->success();
            return redirect()->route('product.index');
        } else {
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    public function get_products_by_subcategory(Request $request)
    {
        $products = Product::where('subcategory_id', $request->subcategory_id)->get();
        return $products;
    }

    public function get_products_by_brand(Request $request)
    {
        $products = Product::where('brand_id', $request->brand_id)->get();
        return view('partials.product_select', compact('products'));
    }

    public function updatePublished(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->published = $request->status;
        $product->save();

        cache_clear();

        return 1;
    }

    public function sku_combination(Request $request)
    {
        // dd($request->all());

        $option_choices = array();

        if ($request->has('product_options')) {
            $product_options = $request->product_options;
            sort($product_options, SORT_NUMERIC);

            foreach ($product_options as $key => $option) {

                $option_name = 'option_' . $option . '_choices';
                $choices = array();

                if ($request->has($option_name)) {

                    $product_option_values = $request[$option_name];
                    sort($product_option_values, SORT_NUMERIC);

                    foreach ($product_option_values as $key => $item) {
                        array_push($choices, $item);
                    }
                    $option_choices[$option] =  $choices;
                }
            }
        }

        $combinations = array(array());
        foreach ($option_choices as $property => $property_values) {
            $tmp = array();
            foreach ($combinations as $combination_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = $combination_item + array($property => $property_value);
                }
            }
            $combinations = $tmp;
        }

        // dd($option_choices,$combinations);

        return view('backend.product.products.sku_combinations', compact('combinations'))->render();
    }

    public function new_attribute(Request $request)
    {
        $attributes = Attribute::query();
        if ($request->has('product_attributes')) {
            foreach ($request->product_attributes as $key => $value) {
                if ($value == NULL) {
                    return array(
                        'count' => -1,
                        'view' => view('backend.product.products.new_attribute', compact('attributes'))->render(),
                    );
                }
            }
            $attributes->whereNotIn('id', array_diff($request->product_attributes, [null]));
        }

        $attributes = $attributes->get();

        return array(
            'count' => count($attributes),
            'view' => view('backend.product.products.new_attribute', compact('attributes'))->render(),
        );
    }

    public function get_attribute_values(Request $request)
    {

        $attribute_id = $request->attribute_id;
        $attribute_values = AttributeValue::where('attribute_id', $attribute_id)->get();

        return view('backend.product.products.new_attribute_values', compact('attribute_values', 'attribute_id'));
    }

    public function new_option(Request $request)
    {

        $attributes = Attribute::query();
        if ($request->has('product_options')) {
            foreach ($request->product_options as $key => $value) {
                if ($value == NULL) {
                    return array(
                        'count' => -1,
                        'view' => view('backend.product.products.new_option', compact('attributes'))->render(),
                    );
                }
            }
            $attributes->whereNotIn('id', array_diff($request->product_options, [null]));
            if (count($request->product_options) === 3) {
                return array(
                    'count' => -2,
                    'view' => view('backend.product.products.new_option', compact('attributes'))->render(),
                );
            }
        }

        $attributes = $attributes->get();

        return array(
            'count' => count($attributes),
            'view' => view('backend.product.products.new_option', compact('attributes'))->render(),
        );
    }

    public function get_option_choices(Request $request)
    {

        $attribute_id = $request->attribute_id;
        $attribute_values = AttributeValue::where('attribute_id', $attribute_id)->get();

        return view('backend.product.products.new_option_choices', compact('attribute_values', 'attribute_id'));
    }

    public function generate_barcode(Request $request)
    {
        $_product_details = Product::where('id', $request->id)->get()->first();
        return view('backend.product.products.barcode', compact('_product_details'));
    }

    public function create_barcode(Request $request)
    {
            $_product_details = Product::where('id', $request->product_id)->get()->first();
            $previous_barcode = Setting::select('value')->where('type', 'serial_no')->get()->first();
            $limit = 10 - strlen($previous_barcode->value);
            $random_numbers = random_int(10 ** (($limit < 0 ? 0:$limit) - 1), (10 ** ($limit < 0 ? 0:$limit)) - 1);
            $previous_barcode_value = $previous_barcode->value == null? 1: $previous_barcode->value;
            $previous_barcode_value = $previous_barcode_value == 1 &&  $previous_barcode->value == null? 1 : $previous_barcode_value + 1;

            // dd($barcode_numbers);
           
            $barcode_arr = [];
            if($request->product_qty == 1)
            {
                $barcode_numbers = "MV".($random_numbers == 0 ? '':$random_numbers). $previous_barcode_value;
                Setting::where('type', 'serial_no')->update([
                    'value' => $previous_barcode_value
                ]);
                array_push($barcode_arr, $barcode_numbers);
                return view('backend.product.products.generated_barcode', compact('barcode_arr', '_product_details'));
            }else if($request->product_qty > 1){
               
                for($i = 0; $i <$request->product_qty; $i++)
                {
                   
                    $previous_barcode_value =intval($previous_barcode_value)  + 1;
                    $limit = 10 - strlen($previous_barcode_value);
                    $random_numbers = random_int(10 ** (($limit < 0 ? 0:$limit) - 1), (10 ** ($limit < 0 ? 0:$limit)) - 1);
                    $barcode_numbers = "MV".($random_numbers == 0 ? '':$random_numbers);
                    array_push($barcode_arr, $barcode_numbers.$previous_barcode_value);
                    
                }
                Setting::where('type', 'serial_no')->update([
                    'value' => $previous_barcode_value
                ]);



            
                return view('backend.product.products.generated_barcode', compact('barcode_arr', '_product_details'));
            }

            

      
       
    }
    //this function take array of integer numbers and sort it as ascending order
    //applying insertion sort
    public function sort_numbers($numbers)
    {
        $item;
        for($i = 1; $i < count($numbers); $i++){
            $item = $numbers[$i];

            $j = $i -1;
            while($j >= 0 &&  $numbers[$j] > $item){
                $numbers[$j+1] =  $numbers[$j];
                $j = $j -1;
            }
            $numbers[$j+1] = $item;
        }
        return $numbers;
    }

    // start function for wastage panel

    public function wastage_home()
    {
        return view('backend.product.products.wastage_home');
    }

    
    //function store wastage product and remove serial from main product row
    public function store_wastage_product(Request $request)
    {
        if($request->ajax())
        {

            $category_class_obj = new CategoryController();
            $serial_num = $category_class_obj->string_to_array_by_comma($request->serial_num);
            $get_unique_data = array_unique($serial_num);
            $wastage_quantity = count($get_unique_data);

            //store wastage data in wastage table
            $wastage_instance = new Wastage();
            $wastage_instance->product_id = $request->product_id;
            $wastage_instance->serial_number =  json_encode($get_unique_data);
            $wastage_instance->product_wastage_qty = $wastage_quantity;
            $wastage_instance->created_by = Auth::user()->id;
            $save_wastage = $wastage_instance->save();

            //remove proudct serial and update qty proudct table
            if($save_wastage){
                $stock_product_serial_number = Product::where('id', $request->product_id)->pluck('serial_no');
                $without_null_stock_serial = $category_class_obj->convert_json_serial_numbers_to_arrays($stock_product_serial_number[0]);

                foreach ($get_unique_data as $key => $order_values) {
                    foreach ($without_null_stock_serial as $key => $value) {
                        if ($value == $order_values) {
                        unset($without_null_stock_serial[$key]);
                        }
                    }
                    
                }

                Product::where('id', $request->product_id)->update([
                    'serial_no' => array_values($without_null_stock_serial),
                    'stock_quantity' =>  count($without_null_stock_serial)
                ]);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Product Wastage store done successfully'
            ]);
        }

    }

    public function wastage_list()
    {
        $wastage_product_info =  Wastage::all();
        return view('backend.product.products.wastage_list', compact('wastage_product_info'));
    }

    public function wastage_details($id)
    {
        $single_wastage_product_info = Wastage::where('id', $id)->first();
        return view('backend.product.products.wastage_details', compact('single_wastage_product_info'));
    }


    //end function for wastage panel



}
