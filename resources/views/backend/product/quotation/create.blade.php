@extends('backend.layouts.app')

@section('content')
<style>
    .main_div{
        background-color: #F0F1F7;
    }
    .inner_div{
        background-color: #ffffff;
    }
    #suggesstion-box li{
      list-style-type: none;
      cursor: pointer;
      padding: 5px;
      padding-left: 30px;
    }
    #suggesstion-box li:hover{
      color: green;
      
    }
    .img_respon{
        max-width: 100px;
        height: auto;
        margin: auto;
    }
    .text_left{
        text-align:left;
    }
    .key_feature p{
        margin-bottom:0px !important;
    }

</style>
    <div class="aiz-titlebar text-left mt-2 mb-3" style="display:none;">
        <div class="row align-items-center">

            <!-- <div class="col-md-6 text-md-right">

            </div> -->
        </div>
    </div>
    <div class="card">
        <div class="card-header" style="position: fixed;width: 100%;background: #ffffff !important; z-index: 96;margin-top: -21px;">
        <div class="row">
            <div class="col-md-12">
                <h1 class="h3">Quotation Create</h1>
            </div>
            <div class="search_box col-md-12 mt-1">
                <div class="form-group">
                    <input id="select_product" type="text" class="form-control"  placeholder="Search By Product Name">
                    <div id="suggesstion-box"></div>
                </div>
            </div>
        </div>
            <div class="pull-right clearfix">

            </div>
        </div>
        <div class="card-body"  style="margin-top: 115px;"> 
        <form action="{{route('quotation.storeQuotaiton')}}" method="POST" >
          @csrf
        <div class="row">
          @if (session()->has('status'))
            <div class=" notification alert alert-success col-md-12">
                {{ session('status') }}
            </div>
          @endif
          
          {{-- editing form start --}}
          @if (Route::current()->getName() == 'quotation.list.edit' || Route::current()->getName() == 'quotation.list.duplicate')
          <div class="col-md-12 product_list">
            
            <h4 class="inline-block text-bold">Select Quotation Type: </h4>
            <div class="form-check">
              <input class="form-check-input " value="0" name="type" type="radio" id="flexRadioDefault1" {{($edit_specefic_quotation['0']['quotation_type'] == 0 ? "checked": '')}}>
              <label class="form-check-label"  for="flexRadioDefault1">
                Dealer
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input "  value="1" name="type" type="radio"  id="flexRadioDefault2" {{($edit_specefic_quotation['0']['quotation_type'] == 1 ? "checked": '')}}>
              <label class="form-check-label" for="flexRadioDefault2">
                Corporate
              </label>
            </div>
          </div>

          <div class="col-12 mt-2">
            <div class="row">
              <div class="form-group col-md-3">
                <label for="">Enter Company Persons</label>
                <input type="text" class="form-control" name="company_persons" value="{{$edit_specefic_quotation['0']['company_persons']}}">
              </div>
              <div class="form-group col-md-3">
                  <label for="">Enter Company Name</label>
                  <input type="text" class="form-control" name="company_name" value="{{$edit_specefic_quotation['0']['company_name']}}" required>
              </div>
              <div class="form-group col-md-3">
                <label for="">Enter Company Adderss</label>
                <input type="text" class="form-control" name="company_address" value="{{$edit_specefic_quotation['0']['company_address']}}" required>
              </div>
              <div class="form-group col-md-3">
                <label for="">Enter  Subject</label>
                <input type="text" class="form-control" name="quotation_subject" value="{{$edit_specefic_quotation['0']['quotation_subject']}}" required>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-3">
                <label for="">Enter  Attention</label>
                <input type="text" class="form-control" name="attention_quot"  value="{{$edit_specefic_quotation['0']['attention_quot']}}">
              </div>
              <div class="form-group col-md-3">
                <label for="">Dear Sir</label>
                <input type="text" class="form-control" name="dear_sir" value="{{$edit_specefic_quotation['0']['dear_sir']}}" >
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-12">
                <label for="">Enter  Quotation body</label>
                <textarea name="quottaion_body"  id=""  rows="2" class="form-control" >{{$edit_specefic_quotation['0']['quottaion_body']}}</textarea>
              </div>
            </div>
          </div>
          {{-- editing form end --}}
          @else
          <div class="col-md-12 product_list">

            <h3 class="inline-block">Select Quotation Type</h3>
            <div class="form-check">
              <input class="form-check-input" value="0" name="type" type="radio" id="flexRadioDefault1">
              <label class="form-check-label"  for="flexRadioDefault1">
                Dealer
              </label>
            </div>
            <div class="form-check  ">
              <input class="form-check-input"  value="1" name="type" type="radio"  id="flexRadioDefault2" checked>
              <label class="form-check-label" for="flexRadioDefault2">
                Corporate
              </label>
            </div>
          </div>

          <div class="col-12 mt-2">
            <div class="row">
              <div class="form-group col-md-3">
                <label for="">Enter Company Persons</label>
                <input type="text" class="form-control" name="company_persons" required>
            </div>
              <div class="form-group col-md-3">
                  <label for="">Enter Company Name</label>
                  <input type="text" class="form-control" name="company_name" required>
              </div>
              <div class="form-group col-md-3">
                <label for="">Enter Company Adderss</label>
                <input type="text" class="form-control" name="company_address" required>
              </div>
              <div class="form-group col-md-3">
                <label for="">Enter  Subject</label>
                <input type="text" class="form-control" name="quotation_subject" required>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-3">
                <label for="">Enter  Attention</label>
                <input type="text" class="form-control" name="attention_quot" >
              </div>
              <div class="form-group col-md-3">
                <label for="">Dear Sir</label>
                <input type="text" class="form-control" name="dear_sir" >
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-12">
                <label for="">Enter  Quotation body</label>
                <textarea name="quottaion_body"  id=""  rows="2" class="form-control"></textarea>
              </div>
            </div>
          </div>
          @endif

            <div class="col-md-12">
            <table class="table table-bordered text-center">
                <thead>
                  <tr>
                    <th scope="col">Product Name</th>
                    <th scope="col">Product Specification</th>
                    <th scope="col">Product Image</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Unit Price</th>
                    <th scope="col">Price</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                   
                    <tbody id="append_div">
                    @php
                      $price=0;
                      $total_price=0;
                     @endphp
                     @if (Route::current()->getName() == 'quotation.list.edit' || Route::current()->getName() == 'quotation.list.duplicate')
                     @php
                      $increment = 101;
                      $i=1;
                     @endphp
                          @foreach ($edit_specefic_quotation as $key =>  $item)
                            @if(!empty($item->product))
                            @php
                              $price = $item->quantity*$item->product_price;
                              $total_price += $price;
                            @endphp

                            <tr style="vertical-align: middle;" class="product_row_{{($increment)}}  id="{{$increment}}">
                              @if (Route::current()->getName() == 'quotation.list.edit')
                                <input type="hidden" name="quotation_number" value="{{$item->quotation_number}}">
                                <input type="hidden" name="is_duplicate" value="">
                              @endif
                              @if (Route::current()->getName() == 'quotation.list.duplicate')
                                <input type="hidden" name="quotation_number" value="{{$item->quotation_number}}">
                                <input type="hidden" name="is_duplicate" value="{{$duplicate_flag}}">
                              @endif
                              
                              <td><input type="hidden" class="row_len" name="id[]" value="{{$item->product->id}}">{{$item->product->name}}</td>
                              <td class="text_left key_feature"><?php echo $item->product->key_feature; ?></td>
                              <td><img class="img_respon" src="{{ uploaded_asset($item->product->photos) }}" alt=""></td>
                              <td><input type="text" onkeyup="price_calculation({{$increment}})" class="form-control quantity quantity_{{$increment}}" name="quantity[]" value="{{$item->quantity}}"></td>
                              <td><input type="text" onkeyup="price_calculation({{$increment}})" class="form-control unit_price_{{$increment}}" name="highest_price[]" value="{{$item->product_price}}"></td>
                              <td style="padding-top: 24px;" class="price_{{$increment}}">{{$price}}</td><input class="price" id="price_{{$increment}}" type="hidden" value="{{$price}}">
                              <td class="text-center" style="width:120px">
                                <a class="dell btn btn-outline-danger btn-sm " style="margin-top: 4px !important;" onclick="deleteRow({{$increment}})">DEL</a>
                              </td>
                            </tr>
                     @php
                      $increment++;
                     @endphp
                     @endif
                          @endforeach
                     @endif                  
                    </tbody>
                    <tr style="vertical-align: middle;">
                        <td colspan="1" class="table_border text-right">
                           
                        </td>
                        <td colspan="4" class="table_border text-right" style="text-align:right;">
                            <b>Total Amount:</b>
                        </td>
                        <td colspan="1" class="table_border text-left" id="total_price" style="text-align:center;">
                        </td>
                    </tr>
                  </table>
                </div>
                
                @if (Route::current()->getName() == 'quotation.list.edit' || Route::current()->getName() == 'quotation.list.duplicate' )
                <div class="col-12 mt-2">
                  <div class="form-group row">
                      <label class="col-md-1 col-from-label">Terms & Condition</label>
                      <div class="col-md-6">
                          <textarea class="aiz-text-editor" name="terms_and_condition">
                             <?php echo $edit_specefic_quotation['0']['terms_and_condition']; ?>
                          </textarea>
                      </div>
                  </div>
                </div>
                @else
                <div class="col-12 mt-2">
                  <div class="form-group row">
                      <label class="col-md-1 col-from-label">Terms & Condition</label>
                      <div class="col-md-6">
                          <textarea class="aiz-text-editor" name="terms_and_condition">
                               <b>•	Validity	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: 07 (Seven) days </b>from the date of submission.
                          </br><b>•	Delivery 	&nbsp;&nbsp;&nbsp;&nbsp;: 07 (Seven) days </b>after receiving the formal work order. 
                          </br><b>•	Warranty	&nbsp;&nbsp;:</b> N/A 		
                          </br><b>•	Payment	  &nbsp;&nbsp;&nbsp;:  </b>50% Advance & 50%  Check on Delivery(After Submission of Bill)
                          </br><b>•	Service	  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:  </b>N/A
                          </br><b>•	VAT & Tax	:</b>Excluding VAT & Tax. 
                          </br><b>•	Prices and availability subject to change without any prior notice</b>
                          </br><b>•	Prices based on Ex.work Elephant Road</b>
                          </br><b>•	T&C Applicable</b>
                          </textarea>
                      </div>
                  </div>
                </div>
                @endif
                
                @if (Route::current()->getName() == 'quotation.list.edit')
                <button class="btn btn-primary text-center m-auto" value="submit">Update</button>
                @elseif (Route::current()->getName() == 'quotation.list.duplicate')
                <button class="btn btn-primary text-center m-auto" value="submit">Duplicate</button>
                @else
                <button class="btn btn-primary text-center m-auto" id="valueJusitfy" value="submit">Submit</button>
                @endif
            </div>
        </form>
        </div>
    </div>
@endsection




@section('script')
<script type="text/javascript">
//hide the submit button before adding product
$('#valueJusitfy').hide();

//retrieve data form backend and do autocomplete search
$('#select_product').on('keyup', function(){
 $("#suggesstion-box").html("");
 var value = $(this).val();
 $.ajax({
   type:"GET",
   url:"{{URL::to('admin/quotation/search')}}",
   data:{'search':value,'id':''},
   success:function(response){

    //  console.warn(response);
     var row_info ="";
     var p_length = response.data.length;   

     for(i=0;i<p_length;i++){
      row_info += '<li onClick="selectProduct('+ response.data[i]["id"]+ ')">'+ response.data[i]["name"]+'</li>';
     }

       $("#suggesstion-box").show();
       if(value !== ""){
         $("#suggesstion-box").html(row_info);
       }
       else{
         $("#suggesstion-box").html("");
       }

     }

 });



});



var inc=1;
function selectProduct(id) {

   $.ajax({
   type:"GET",
   url:"{{URL::to('admin/quotation/search')}}",
   data:{'search':"",'id':id},
   success:function(response){
     
    console.warn(response.image_url.file_name);
     var price=0;
       if($('#flexRadioDefault1').is(':checked')){
           price = response.dealer_price;

         }else{
           price = response.corporate_price;

       }
       if(price ==null){
         price =0;
       }
     var base_url = window.location.origin;
     var row = '<tr style="vertical-align: middle;" class="product_row_'+inc+'" id="'+inc+'">\
         <td class="text-justify">'+response.name+' <input type="hidden" name="id[]" class="row_len" value="'+response.id+'"></td>\
         <td class="text-justify key_feature">'+response.key_feature+'</td>\
         <td> <img class="img_respon" src="'+ base_url+"/public/"+response.image_url.file_name+'" alt=""></td>\
         <td><input type="text" onkeyup="price_calculation('+inc+')"  class="form-control quantity quantity_'+inc+'" name="quantity[]" value="1"></td>\
         <td><input type="text" onkeyup="price_calculation('+inc+')" class="form-control unit_price_'+inc+'" name="highest_price[]" value="'+price+'"></td>\
         <td style="padding-top: 24px;" class="price_'+inc+'">'+price+'</td><input class="price" id="price_'+inc+'" type="hidden" value="'+price+'">\
         <td class="text-center" style="width:120px" ><a style="margin-top: 4px !important;"  onclick="deleteRow('+inc+')" class="dell btn btn-outline-danger btn-sm mt-3">DEL</a></td>\
       </tr>';

       var newValue = 0;
      
       $('.row_len').each(function(){
         if(this.value == response.id){
           newValue++;     
         }
       });


       if(!(newValue == 1)){
          $('#append_div').append(row);
          $('#select_product').val('');
        }

        
      
     
     inc++;
     calculation();
     }
     
  

 });
$("#suggesstion-box").hide();
//show submit button if data is added
$('#valueJusitfy').show();


}

//remove product list function
function deleteRow(value){
 $('.product_row_'+value).remove();

 //delete submit button if no data is available
 if ( $('#append_div').children().length < 1 ) {
 $('#valueJusitfy').hide();
 }
 calculation();
}

//set notification after save data to db
removeNotification();
function removeNotification(){
 setTimeout(() => {
   $('.notification').remove();
 }, 3000);
}

function price_calculation(id){
 var quantity = $('.quantity_'+id).val();
 var price = $('.unit_price_'+id).val();
 
  if($('.quantity_'+id).val() <= 0){
      $('.quantity_'+id).val(1);
      quantity=1;
      calculation();
    }

  $('.price_'+id).html(quantity*price);
  $('#price_'+id).val(quantity*price);
 calculation();
}

function calculation(){

var total_price = 0 ;
  $( ".price" ).each(function() {
    total_price += Number($(this).val());
  });

  $("#total_price").html(total_price);
}

$( document ).ready(function() {

  calculation();

});
</script>
@endsection
 