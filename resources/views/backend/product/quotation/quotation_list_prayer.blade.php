<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <title>Document</title>
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
</head>
<body>
    <div class="card">
        <div class="card-body"> 
        <div class="row">
        <div class="container-fluid pl-3 pr-3 pt-1 auth_check" id="{{(Auth::check()) ? '1':'0' }}">
    <div class="img m-0">
        <img class="rounded m-0 img_respon_head" src="{{asset('images/quotation_heading.jpg')}}" alt="">
    </div>
    
    <div>
        <div class="date_div m-0 p-0">
            <div class="pr-1">
                Date: 
            </div>
            <div>
                {{date('d-m-Y', strtotime($specefic_quotation_number_products['0']['created_at']))}}
            </div>
        </div>
        
    </div>
    <div>
        <button class="btn btn-success no-print mt-1 " onclick="printFun()"><i class="fa fa-print" aria-hidden="true"></i> Print</button> 
    </div>
    <div class="mt-5">
        <p class="mb-0" style="font-size: 18px">To</p>
        @if ($specefic_quotation_number_products['0']['company_persons'] != null)
            <p style="font-size: 18px" class="text-bold mb-0">{{$specefic_quotation_number_products['0']['company_persons']}}</p>
        @endif
        <p class="text-bold mb-0" style="font-size: 18px">{{$specefic_quotation_number_products['0']['company_name']}}</p>
        <p class="mb-0" style="font-size: 18px">{{$specefic_quotation_number_products['0']['company_address']}}</p>
        <p class="mb-0" style="font-size: 18px"><span class="text-bold">Kind Attention: </span>Concern person of procurement</p>
        <p class="text-bold" style="font-size: 18px"><b>Subject: <u> {{$specefic_quotation_number_products['0']['quotation_subject']}}</u></b></p>
        <p class="mb-0" style="font-size: 18px">Dear Sir,  </p>
        <p class="text-justify" style="font-size: 18px">We are pleased to submit our offer for the below products. Enclosed herewith for your organization. It may be mentioned here that, we supply Computer & IT Products for more than 24 years and have got a reputation for our quality product and excellent after-sales-service. </p>

    </div>
    <div class="mt-2">
        <p style="font-size: 18px;text-align: justify;">Our priority products are <b>DELL</b>, <b>LENOVO</b>, <b>CANON</b>, <b>EPSON</b>, <b>HP</b>, <b>MI</b>, <b>PANTUM</b>, <b>LOGITECH</b>, <b>SAMSUNG</b>, <b>INTEL</b>, <b>SONY</b>, <b>INFINIX</b>, <b>APPLE</b>, <b>BROTHER</b>, <b>RYZEN</b>, <b>TOSHIBA</b>, <b>CORSAIR</b>, <b>POWER TREE</b>, etc.We deals with 100+ Brands.</p>
    </div>
    <div>
        <p style="font-size: 18px;text-align: justify;">We trust that our offer will meet your requirement and you will favor us with your valued work order.</p>
        <br>
        <br>
        <p style="font-size: 18px">Thanking you,</p>
        <p class="mb-0" style="font-size: 18px">Your faithfully,</p>
        <p class="mb-0 mt-5" style="font-size: 18px;">{{$specefic_quotation_number_products['0']->user->name}}</p>
        <p class="mb-0" style="font-size: 18px">Email: {{$specefic_quotation_number_products['0']->user->email}}</p>
        <p style="font-size: 18px">Mobile: {{$specefic_quotation_number_products['0']->user->phone}}</p>
    </div>
    
</div>
    



    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
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
</body>
</html>