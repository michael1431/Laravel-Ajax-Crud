<!DOCTYPE html>
<html>
 <head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Laravel 5.8 - Ajax Crud Application </title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
  <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>  
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" />
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
 </head>
 <body>
  <div class="container">    
     <br />
     <h3 align="center">Laravel AJax Crud Application</h3>
     <br />
     <div align="right">
      <button type="button" name="create_record" id="create_record" class="btn btn-success btn-sm">Create Record</button>
     </div>
     <br />
   <div class="table-responsive">
    <table class="table table-bordered table-striped" id="user_table">
           <thead>
            <tr>
                <th width="10%">Image</th>
                <th width="35%">First Name</th>
                <th width="35%">Last Name</th>
                <th width="30%">Action</th>
            </tr>
           </thead>
       </table>
   </div>
   <br />
   <br />
  </div>
 </body>
</html>
{{-- Add/Edit modal starts--}}
<div id="formModal" class="modal fade" role="dialog">
 <div class="modal-dialog">
  <div class="modal-content">
   <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Add New Record</h4>
        </div>
        <div class="modal-body">
         <span id="form_result"></span>
         <form method="post" id="sample_form" class="form-horizontal" enctype="multipart/form-data">
          @csrf
          <div class="form-group">
            <label class="control-label col-md-4" >First Name : </label>
            <div class="col-md-8">
             <input type="text" name="first_name" id="first_name" class="form-control" />
            </div>
           </div>
           <div class="form-group">
            <label class="control-label col-md-4">Last Name : </label>
            <div class="col-md-8">
             <input type="text" name="last_name" id="last_name" class="form-control" />
            </div>
           </div>
           <div class="form-group">
            <label class="control-label col-md-4">Select Profile Image : </label>
            <div class="col-md-8">
             <input type="file" name="image" id="image" />
             <span id="store_image"></span>
            </div>
           </div>
           <br />
           <div class="form-group" align="center">
            <input type="hidden" name="action" id="action" />
            <input type="hidden" name="hidden_id" id="hidden_id" />
            <input type="submit" name="action_button" id="action_button" class="btn btn-warning" value="Submit" />
           </div>
         </form>
        </div>
     </div>
    </div>
</div>
{{-- Add/Edit modal ends--}}

{{-- Delete modal starts--}}
<div id="confirmModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h2 class="modal-title">Confirmation</h2>
            </div>
            <div class="modal-body">
                <h4 align="center" style="margin:0;">Are you sure you want to remove this data?</h4>
            </div>
            <div class="modal-footer">
            <input type="submit" name="ok_button" id="ok_button" class="btn btn-danger" value="OK" />
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
{{-- Delete modal ends--}}

<script>
$(document).ready(function(){
/////// show all the records section starts 

// initialize the datatable plugin
 $('#user_table').DataTable({
  // enable process table data   
  processing: true,
  // enable server side processing
  serverSide: true,
  ajax:{
   url: "{{ route('ajax-crud.index') }}",
  },
  // define table column details
  columns:[   
   {
    data: 'image',
    name: 'image',
    // display image in a table
    render: function(data, type, full, meta){
     return "<img src={{ URL::to('/') }}/images/" + data + " width='70' class='img-thumbnail' />";
    },
    // disable image column sorting
    orderable: false
   },
   {
    data: 'first_name',
    name: 'first_name'
   },
   {
    data: 'last_name',
    name: 'last_name'
   },
   {
    data: 'action',
    name: 'action',
    orderable: false
   }
  ]
 });
 /////// show all the records section ends

  // when the user click the add button
  $('#create_record').click(function(){
    // Add the button value
     $('#action_button').val("Add");
    // store the value Add in the hidden field 
     $('#action').val("Add");
    // reset the title
    $('.modal-title').text("Add New Record");
    // clear the form contents
    $('#sample_form')[0].reset();
    // clear the image
    $('#store_image').html('');
    // show modal
     $('#formModal').modal('show');
 });

  // when user submit the form for both ADD And EDIT
  $('#sample_form').on('submit', function(event){
    event.preventDefault();
    /////// Add records section starts
    if($('#action').val() == 'Add')
    {
    $.ajax({
        url:"{{ route('ajax-crud.store') }}",
        method:"POST",
        data: new FormData(this),
        contentType: false,
        cache:false,
        processData: false,
        dataType:"json",
        success:function(data)
        {
        var html = '';
        if(data.errors)
        {
        html = '<div class="alert alert-danger">';
        for(var count = 0; count < data.errors.length; count++)
        {
        html += '<p>' + data.errors[count] + '</p>';
        }
        html += '</div>';
        }
        if(data.success)
        {
        html = '<div class="alert alert-success">' + data.success + '</div>';
        // clear the form contents
        $('#sample_form')[0].reset();
        // refresh data table data
        $('#user_table').DataTable().ajax.reload();
        }
        // error or success msg show
        $('#form_result').html(html);
        }
    })
  }
   /////// Add records section ends

  /////// Edit record section starts
  ///// when user click the edit button, data have been changed
  if($('#action').val() == "Edit")
  {
   $.ajax({
    url:"{{ route('ajax-crud.update') }}",
    method:"POST",
    data:new FormData(this),
    contentType: false,
    cache: false,
    processData: false,
    dataType:"json",
    success:function(data)
    {
     var html = '';
     if(data.errors)
     {
      html = '<div class="alert alert-danger">';
      for(var count = 0; count < data.errors.length; count++)
      {
       html += '<p>' + data.errors[count] + '</p>';
      }
      html += '</div>';
     }
     if(data.success)
     {
      html = '<div class="alert alert-success">' + data.success + '</div>';
      // clear the form contents
      $('#sample_form')[0].reset();
      // clear the image
      $('#store_image').html('');
      $('#user_table').DataTable().ajax.reload();
     }
     $('#form_result').html(html);

    }
   });
  }

  });  


 /////// show individual records when user click the edit button
  $(document).on('click', '.edit', function(){
    var id = $(this).attr('id');
    $('#form_result').html('');
    $.ajax({
    url:"/ajax-crud/"+id+"/edit",
    dataType:"json",
    success:function(html){
        $('#first_name').val(html.data.first_name);
        $('#last_name').val(html.data.last_name);
        $('#store_image').html("<img src={{ URL::to('/') }}/images/" + html.data.image + " width='70' class='img-thumbnail' />");
        $('#store_image').append("<input type='hidden' name='hidden_image' value='"+html.data.image+"' />");
        $('#hidden_id').val(html.data.id);
        $('.modal-title').text("Edit Record");
        $('#action_button').val("Edit");
        $('#action').val("Edit");
        $('#formModal').modal('show');
    }
    })
 });
/////// Edit record section ends

 var user_id;
// when user click the delete button modal has been showed
 $(document).on('click', '.delete', function(){
    user_id = $(this).attr('id');
    $('#confirmModal').modal('show');
    $('.modal-title').text("Confirmation");

 });
  // after click the ok button the record is deleted after 2s
  $('#ok_button').click(function(){
    $.ajax({
    url:"ajax-crud/destroy/"+user_id,
    success:function(data)
    {
        setTimeout(function(){
        $('#confirmModal').modal('hide');
        $('#user_table').DataTable().ajax.reload();
        }, 1000);
    }
    
    })
 });

}); 
</script>
