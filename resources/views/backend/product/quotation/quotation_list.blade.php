@extends('backend.layouts.app')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css"/>
@section('content')
<style>

</style>
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-12">
            <h2 class="bg-primary  text-center" style="color:white;">QUOTATION LIST</h2>
                @if (session()->has('status'))
                <div class=" notification alert alert-success col-md-12">
                    {{ session('status') }}
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="pull-right clearfix">

            </div>
        </div>
        <div class="card-body"> 
        <div class="row">
        <div class="col-md-12 pr-3">
            <table id="dtBasicExample" class="table table-striped table-bordered table-sm p-2 text-center" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th scope="col">SL</th>
                    <th scope="col">Quotation Number</th>
                    <th scope="col">Quotation Type</th>
                    <th scope="col">Name</th>
                    <th scope="col">Created Date</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody>
                  @php
                    $previous_qn = "";
                    $i=1;
                  @endphp

                  @foreach ($qutotation_list_data as $key => $item)
                  @php
                  if($previous_qn !== $item->quotation_number){
                  @endphp
                  <tr>
                    <th scope="row">{{$i}}</th>
                    <td>{{$item->quotation_number}}</td>
                    <td>{{($item->quotation_type == 0)? "DEALER": "CORPORATE"}}</td>
                    <td>{{$item->company_name}}</td>
                    <td>{{date('d-m-Y', strtotime($item->created_at))}}</td>
                    <td>
                      <!-- <a href="{{url('/quotation/list/details', $item->quotation_number)}}"  target="_blank" class="btn btn-primary btn-sm">Details</a> -->
                      <?php 
                      // $quotation_number= Crypt::encrypt($item->quotation_number); 
                      $quotation_number = bin2hex($item->quotation_number);
                      ?>
                      <a href="{{url('/quotationc/list/details', $quotation_number)}}"  target="_blank" class="btn btn-primary btn-sm">Details</a>
                      <a href="{{url('/quotationc/list/liflet', $quotation_number)}}" target="_blank" class="btn btn-primary btn-sm">Liflet</a>
                      <a href="{{url('admin/quotation/list/edit', $item->quotation_number)}}" class="btn btn-info btn-sm">Edit</a>
                      <a href="{{url('admin/quotation/list/duplicate', $item->quotation_number)}}" class="btn btn-info btn-sm">Duplicate</a>
                      <a href="{{url('quotation/list/prayer', $quotation_number)}}" target="_blank" class="btn btn-info btn-sm">Prayer</a>
                      <a href="{{url('admin/quotation/list/delete', $item->quotation_number)}}"  class="btn btn-danger btn-sm delete-confirm">Delete</a>
                    </td>
                  </tr>
                  @php
                  $previous_qn = $item->quotation_number;
                  $i++;
                    }
                  @endphp
                  @endforeach
                </tbody>
              </table>
        </div>
    </div>
        </div>
    </div>
@endsection




@section('script')
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
$(document).ready(function () {
    $('#dtBasicExample').DataTable();
    $('.dataTables_length').addClass('bs-select');


//delete Confirmation code
$('.delete-confirm').click(function(event){
   event.preventDefault();
   var url = $(this).attr('href');
    swal({
    title: "Are you sure?",
    text: "Once deleted, you will not be able to recover !",
    icon: "warning",
    buttons: true,
    dangerMode: true,
    })
    .then((willDelete) => {
    if (willDelete) {
        window.location.href = url;
        swal("Your quotation has been deleted!", {
        icon: "success",
        });
    } else {
        swal("Your quotation is safe!");
    }
    });

});


});




//remove notification after save data to db
removeNotification();
function removeNotification(){
  setTimeout(() => {
    $('.notification').remove();
  }, 3000);
}


</script>
@endsection
