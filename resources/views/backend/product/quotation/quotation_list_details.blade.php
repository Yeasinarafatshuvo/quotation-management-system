@extends('backend.layouts.app')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
@section('content')
<style>

hr{
        height: 2px;
        border-width: none;
        color: gray;
        background-color: gray;

    }
    .boxedd {
        border: 1px solid green ;
    }
    .date_div {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .table_head{
        background-color: gray;
    }
    .table_border{
        border: 1px solid black !important;
    }
    .no-print{
        margin-left: 45%;
        height: 30px;
        width: 150px;
        padding: 3px;
        /* border-radius: 5%; */
        
    }
    .img_respon{
        max-width: 50%;
        height: auto;
        margin: auto;
    }
    .img_respon_head{
        max-width: 100%;
        height: auto;
        margin: auto;
    }
    .client_design{
        padding-right: 20px !important;
    }
   
  
    @media print{
        body{
            -webkit-print-color-adjust: exact;
        }
        .no-print{
            display: none;
        }
        .aiz-topbar{
            display: none !important;
        }
        .aiz-content-wrapper {
            padding-top: 0px !important;
        }
        .table_head{
            background-color: #7a5ce6;
            color:white;
        }
        
    }
   
</style>
    <div class="card">
        <div class="card-body"> 
        <div class="row">
        <div class="container-fluid pl-3 pr-3 pt-1 auth_check" id="{{(Auth::check()) ? '1':'0' }}">
    <div class="img m-0">
        <img class="rounded m-0 img_respon_head" src="{{asset('images/quotation_heading.jpg')}}" alt="">
    </div>
    <hr class="mt-1 mb-0">
    <div>
        <div class="date_div m-0 p-0">
            <div class="pr-1">
                Date: 
            </div>
            <div>
                {{date('d-m-Y', strtotime($specefic_quotation_number_products['0']['created_at']))}}
            </div>
            <div>
                <p class="m-0"> (Prices Valid for 3 days)</p>
            </div>
        </div>

        <div class="boxedd text-center">
            <!--<h6 class="font-weight-bold pt-2">PLEASE CALL FOR SPECIAL PRICES</h6>-->
            <h6 class="font-weight-bold pt-2">FEEL FREE TO CONTACT WITH US</h6>
        </div> 
       
    </div>
    <div>
        <button class="btn btn-success no-print mt-1 " onclick="printFun()"><i class="fa fa-print" aria-hidden="true"></i> Print</button> 
    </div>
    <div class="mt-2">
        <p class="mb-0"><b>Ref No: <span class="text-bold">{{$specefic_quotation_number_products['0']['quotation_number']}}</span></b></p>
        <p class="mb-0">To</p>
        @if ($specefic_quotation_number_products['0']['company_persons'] != null)
            <p class="text-bold mb-0">{{$specefic_quotation_number_products['0']['company_persons']}}</p>
        @endif
        <p class="text-bold mb-0">{{$specefic_quotation_number_products['0']['company_name']}}</p>
        <p class="mb-0">{{$specefic_quotation_number_products['0']['company_address']}}</p>
        <p class="mb-0"><span class="text-bold">Kind Attention: </span>Concern person of procurement</p>
        <p class="text-bold"><b>Subject: <u> {{$specefic_quotation_number_products['0']['quotation_subject']}}</u></b></p>
        <p class="mb-0">Dear Sir,  </p>
        <p class="text-justify">we are pleased to submit our offer for the below products. Enclosed herewith for your organization. It may be mentioned here that, we supply computer & IT Products for more than 24 years and have got a reputation for our quality product and excellent after-sales-service. </p>

    </div>
    <div class="row mt-2">
        <div class="col-md-12 ">
            <table class="table table-bordered text-center ">
                <div >
                    <thead class="table_head" style="background-color: #7a5ce6;color:white;">
                        <tr >
                             <th class="table_border ">SL</th>
                             <th class="table_border"><nobr>Product Name</nobr></th>
                             <th class="table_border"><nobr>Product Specification</nobr></th>
                             <th class="table_border"><nobr>Product Image</nobr></th>
                             <th class="table_border"><nobr>Price(BDT)</nobr></th>                       
                        </tr>
                     </thead>
                </div>
                <tbody>
                    @foreach ($specefic_quotation_number_products as $key => $item)
                    <tr>
                        <td class="table_border data_one">{{($key +1)}}</td>
                        <td class="table_border data_two">{{$item->product->name}}</td>
                        <td class="table_border data_three">{{$item->product->meta_description}}</td>
                        <td class="table_border" ><img class="img_respon" src="{{ uploaded_asset($item->product->photos) }}" alt=""></td>
                        <td class="table_border data_five">{{$item->product_price}}</td>                   
                    </tr>
                    
                    @endforeach
                    <tr>
                        <td colspan="2" class="table_border text-right">
                            <p class="text-danger">Terms and Conditions:</p>
                        </td>
                        <td colspan="3" class="table_border text-left">
                            <ol>
                                <li>Prices and availability subject to change without any prior notice.</li>
                                <li>Prices based on Ex.work Elephant Road.</li>
                                <li>TAX and VAT excluded</li>
                                <li>Payment Terms: CASH.</li>
                                <li>T&C Applicable.</li>
                            </ol>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div>
        <p>We trust that our offer will meet your requirement and you will favor us with your valued work order.</p>
        <br>
        <br>
        <p>Thanking you,</p>
        <p class="mb-0">your faithfully,</p>
        <p class="mb-0">{{$specefic_quotation_number_products['0']->user->name}}</p>
        <p class="mb-0">Email: {{$specefic_quotation_number_products['0']->user->email}}</p>
        <p>Mobile: {{$specefic_quotation_number_products['0']->user->phone}}</p>
    </div>
  
</div>

    </div>
        </div>
    </div>
@endsection




@section('script')
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
let printFun = () => {
    window.print();
    
}

$(document).ready(function (e) {
   
    var auth_value = $('.auth_check').attr('id');
    if(!(auth_value == 1)){
       $('aside').css("display", "none");
       $('.nav-link').css("display", "none");
       $('#pushmenu').trigger('click');
       $('.auth_check').addClass('client_design');
    }
});
</script>
@endsection
