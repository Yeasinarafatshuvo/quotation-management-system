<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

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
        max-width: 100px;
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
       .text-left{
        text-align:left;
    }
    .card-body {
        flex: 1 1 auto;
        padding: 1rem 5rem;
    }
    .text_left{
        text-align:left;
    }    
    .key_feature p{
        margin-bottom:0px !important;
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
        body{
            font-size:10px;
        }
        table{
            line-height: 16px  !important;
            font-size:10px !important;
        }
        table td{
            line-height: 16px !important;
            font-size:10px !important;
        }
        .table_head{
            background-color: #7a5ce6;
            color:white;
        }
        .card-body {
            flex: 1 1 auto;
            padding: 1rem 1rem;
        }
        .text_left{
            text-align:left;
        }
        .img_respon{
            max-width: 25px;
            height: auto;
            margin: auto;
        }  
        .key_feature p{
        margin-bottom:0px !important;
        }    
   
        
    }
   
</style>
    <div class="card">
        <div class="card-body"> 
        <div class="row">
        <div class="container-fluid pl-3 pr-3 pt-1 auth_check" id="{{(Auth::check()) ? '1':'0' }}">
    <div class="img m-0">
        <img class="rounded m-0 img_respon_head" src="{{asset('public/images/quotation_heading.jpg')}}" alt="">
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
            <h6 class="font-weight-bold pt-0" style="padding:0;margin:0;">FEEL FREE TO CONTACT WITH US</h6>
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
        
        @if (!empty($specefic_quotation_number_products['0']['attention_quot']))
            <p class="mb-0"><span class="text-bold">{{$specefic_quotation_number_products['0']['attention_quot']}}</p>
        @else
            <p class="mb-0"><span class="text-bold">Kind Attention: </span>Concern person of procurement</p>
        @endif
        
        <p class="text-bold"><b>Subject: <u> {{$specefic_quotation_number_products['0']['quotation_subject']}}</u></b></p>

        @if (!empty($specefic_quotation_number_products['0']['dear_sir']))
            <p class="mb-0">{{$specefic_quotation_number_products['0']['dear_sir']}},  </p>
        @else
            <p class="mb-0">Dear Sir,  </p>
        @endif

        @if (!empty($specefic_quotation_number_products['0']['quottaion_body']))
            <p class="text-justify">{{$specefic_quotation_number_products['0']['quottaion_body']}}</p>
        @else
            <p class="text-justify">we are pleased to submit our offer for the below products. Enclosed herewith for your organization. It may be mentioned here that, we supply computer & IT Products for more than 24 years and have got a reputation for our quality product and excellent after-sales-service. </p>
        @endif
       
    </div>
    <div class="row mt-2">
        <div class="col-md-12 ">
            <table class="table table-bordered text-center " style="line-height: 16px;font-size: 12px;">
                <div >
                    <thead class="table_head" style="background-color: #7a5ce6;color:white;">
                        <tr>
                             <th class="table_border">SL</th>
                             <th class="table_border"><nobr>Product Name</nobr></th>
                             <th class="table_border"><nobr>Product Specification</nobr></th>
                             <th class="table_border"><nobr>Product Image</nobr></th>
                             <th class="table_border"><nobr>QTY(pcs)</nobr></th>                       
                             <th class="table_border"><nobr>Unit Price(BDT)</nobr></th>                       
                             <th class="table_border"><nobr>Price(BDT)</nobr></th>                       
                        </tr>
                     </thead>
                </div>
                <tbody>
                    @php 
                    $price=0;
                    $total_price=0;
                    $i=1;
                    @endphp

                    @foreach ($specefic_quotation_number_products as $key => $item)
                    <tr style="vertical-align: middle;">
                    @if(!empty($item->product))
                    @php 
                    $price = $item->quantity*$item->product_price;
                    $total_price += $price;
                    @endphp
                        <td style="padding-top: 0; padding-bottom: 0px;line-height: 16px;font-size: 12px;" class="table_border data_one">{{($i)}}</td>
                        <td style="padding-top: 0; padding-bottom: 0px;line-height: 16px;font-size: 12px;" class="table_border data_two">{{$item->product->name}}</td>
                        <td style="padding-top: 0; padding-bottom: 0px;line-height: 16px;font-size: 12px;" class="table_border data_three text_left key_feature"><?php echo $item->product->key_feature; ?></td>
                        <td style="padding-top: 0; padding-bottom: 0px;line-height: 16px;font-size: 12px;" class="table_border" ><img style="max-width: 50px;" class="img_respon" src="{{ uploaded_asset($item->product->photos) }}" alt=""></td>
                        <td style="padding-top: 0; padding-bottom: 0px;line-height: 16px;font-size: 12px;" class="table_border data_five">{{$item->quantity}}</td>                   
                        <td style="padding-top: 0; padding-bottom: 0px;line-height: 16px;font-size: 12px;" class="table_border data_five">{{$item->product_price}}</td>                   
                        <td style="padding-top: 0; padding-bottom: 0px;line-height: 16px;font-size: 12px;" class="table_border data_five">{{$price}}.00</td>                   
                    </tr>
                    @php 
                    $i++;
                    @endphp
                    @endif
                    @endforeach
                    <tr style="vertical-align: middle;" >
                        <td colspan="1" class="table_border text-right">
                           
                        </td>
                        <td colspan="5" class="table_border text-right" style="text-align:right;">
                            <h4>Total Amount:</h4>
                        </td>
                        <td colspan="1" class="table_border" style="text-align:center;">
                            <?php echo $total_price.".00"; ?>
                        </td>
                    </tr>
                    @php
                        function getBangladeshiCurrency(float $number)
                        {
                            $decimal = round($number - ($no = floor($number)), 2) * 100;
                            $hundred = null;
                            $digits_length = strlen($no);
                            $i = 0;
                            $str = array();
                            $words = array(0 => '', 1 => 'one', 2 => 'two',
                                3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
                                7 => 'seven', 8 => 'eight', 9 => 'nine',
                                10 => 'ten', 11 => 'eleven', 12 => 'twelve',
                                13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
                                16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
                                19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
                                40 => 'forty', 50 => 'fifty', 60 => 'sixty',
                                70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
                            $digits = array('', 'hundred','thousand','lakh', 'crore');
                            while( $i < $digits_length ) {
                                $divider = ($i == 2) ? 10 : 100;
                                $number = floor($no % $divider);
                                $no = floor($no / $divider);
                                $i += $divider == 10 ? 1 : 2;
                                if ($number) {
                                    $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                                    $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                                    $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
                                } else $str[] = null;
                            }
                            $taka = implode('', array_reverse($str));
                            $paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
                            return ($taka ? $taka . 'Taka ' : '') . $paise;
                        }
                    @endphp
                    <tr style=" border: 1px solid;">
                        <td colspan="2" style="font-weight: bold">In Word:</td>
                        <td colspan="10" class="text-left">{{ucfirst(getBangladeshiCurrency($total_price))}}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="table_border text-right">
                            <p class="text-danger">Terms and Conditions:</p>
                        </td>
                        <td colspan="5" class="table_border text-left">
                            <?php echo $specefic_quotation_number_products['0']['terms_and_condition']; ?>
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
        <p class="mb-0">Your faithfully,</p>
        <p class="mb-0">{{$specefic_quotation_number_products['0']->user->name}}</p>
        <p class="mb-0">Email: {{$specefic_quotation_number_products['0']->user->email}}</p>
        <p>Mobile: {{$specefic_quotation_number_products['0']->user->phone}}</p>
    </div>
  
</div>

    </div>
        </div>
    </div>

<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
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

