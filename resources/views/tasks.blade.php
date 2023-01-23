<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tasks') }}
        </h2>
    </x-slot>

    <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="card card-new-task">
                <div class="card-header">New Task</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('tasks.store') }}" id="forminsert">
                        @csrf
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input id="title" name="title" type="text" maxlength="255" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" autocomplete="off" required/>
                            @if ($errors->has('title'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('title') }}</strong>
                                </span>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-primary jedit-sub" style="margin: 10px 0px 0px 260px;background: #2c3e50;color: #fff;font-size: 18px;border:0;padding: 8px 30px;border-radius: 3px;cursor: pointer;" id="">Create</button>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header">Tasks</div>

                <div class="card-body">
                   <table class="table table-striped" width="100%" cellpadding="10px">
                   <tr>
            <th width="40px">Id</th>
            <th>Title</th>
            <th>Mark</th>
            <th width="60px">Edit</th>
            <th width="70px">Delete</th>
          </tr>
          <tbody id="load-table"></tbody>
                      
                   </table>

                    {{ $tasks->links() }}
                </div>
            </div>
        </div>
    </div>
</div>


<div id="error-message" class="messages"></div>
  <div id="success-message" class="messages"></div>

  <!-- Popup Modal Box for Update the Records -->
  <div id="modal">
    <div id="modal-form">
      <h2>Edit Form</h2>
      <form action="" id="edit-form">
      <table cellpadding="10px" width="100%">
        <tr>
          <td width="90px">Title</td>
          <td><input type="text" name="title" id="edit-name" required>
              <input type="text" name="id" id="edit-id" hidden="">
          </td>
        </tr>
        <tr>
          <td>Mark</td>
          <td><select name="is_complete" id="edit-age" required>
            <option value="1">Complete</option>
            <option value="0">Not Complete</option>
</select>
          </td>
        </tr>
       
        <tr>
          <td></td>
          <td><input type="button" id="edit-submit" value="Update"></td>
        </tr>
      </table>
      </form>
      <div id="close-btn">X</div>
    </div>
  </div>
</x-app-layout>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
<script>

$(document).ready(function(){
 
//Fetch All Records
function loadTable(){ 
    $("#load-table").html("");
    $.ajax({ 
      url : "tasks/show",
      type : "GET",
      dataType: 'json',
      success : function(data){
        if(data.status == false){
          $("#load-table").append("<tr><td colspan='6'><h2>"+ data.message +"</h2></td></tr>");
        }else{
          $.each(data, function(key, value){ 
            if(value.is_complete == true){
            $("#load-table").append("<tr>" + 
                                    "<td>" + value.id + "</td>" + 
                                    "<td><s>" + value.title+"</s></td>" + 
                                    "<td>Task Compeleted</td>" + 
                                    "<td><button class='edit-btn btn btn-info' data-eid='"+ value.id + "'>Edit</button></td>" + 
                                    "<td><button class='delete-btn btn btn-danger' data-id='"+ value.id + "'>Delete</button></td>" + 
                                    "</tr>");}
                                    else{
                                        $("#load-table").append("<tr>" + 
                                    "<td>" + value.id + "</td>" + 
                                    "<td>" + value.title+"</td>" + 
                                    "<td><button class='com-btn btn btn-primary' data-cid='"+ value.id + "'>Complete</button></td>" + 
                                    "<td><button class='edit-btn btn btn-info' data-eid='"+ value.id + "'>Edit</button></td>" + 
                                    "<td><button class='delete-btn btn btn-danger' data-id='"+ value.id + "'>Delete</button></td>" + 
                                    "</tr>");
                                    }
          });
        }
      }
    });
  }

  loadTable();

  //Show Success or Error Messages
  function message(message, status){
    if(status == true){
      $("#success-message").html(message).slideDown();
      $("#error-message").slideUp();
      setTimeout(function(){
        $("#success-message").slideUp();
      },4000);

    }else if(status == false){
      $("#error-message").html(message).slideDown();
      $("#success-message").slideUp();
      setTimeout(function(){
        $("#error-message").slideUp();
      },4000);
    }
  }

 //update form
$("#forminsert").submit(function(e){

    $.ajax({
        url: $('#forminsert').attr('action'),
        data: $('#forminsert').serialize(),
        type: 'post',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
            message(data.message, data.status);
            if(data.status == true){
          loadTable();
          $("#forminsert").trigger("reset");
        }
        }
    });
    //return 1;
    e.preventDefault();
   
});

//from complete
$(document).on("click",".com-btn",function(){
    var studentId = $(this).data("cid");
      var obj = {id : studentId};
 $.ajax({
     url: 'tasks/update',
     data: obj,
     type: 'PUT',
     headers: {
         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
     },
     success: function(data) {
        message(data.message, data.status);
            if(data.status == true){
          loadTable();
        }
     }
 });
 //return 1;
 e.preventDefault();
 loadTable();
});

 //Delete Record
 $(document).on("click",".delete-btn",function(){
    if(confirm("Do you really want to delete this record ? ")){
      var studentId = $(this).data("id");
      var obj = {id : studentId};
      

      var row = this;

      $.ajax({
      url : 'tasks/destroy',
      type : "DELETE",
      data : obj,
      headers: {
         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
     },
      success : function(data){
        message(data.message, data.status);

        if(data.status == true){
          $(row).closest("tr").fadeOut();
        }
      }
    });
    }
  });

  //Fetch Single Record : Show in Modal Box
  $(document).on("click",".edit-btn",function(){
    $("#modal").show();
    var studentId = $(this).data("eid");
    var obj = {id : studentId};
    

    $.ajax({
      url :  "{{ route('task.edits') }}",
      type : "get",
      data : obj,
      headers: {
         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
     },
      success : function(data){
        $("#edit-id").val(data.id);
        $("#edit-name").val(data.title);
        if(data.is_complete == true){
        $("#edit-age").val(1);}
        else{
            $("#edit-age").val(0)
        }
       
      }
    });
  });

 //Hide Modal Box
 $("#close-btn").on("click",function(){
    $("#modal").hide();
  });

  //Update Record
  $("#edit-submit").on("click",function(e){
    e.preventDefault();

    var jsonObj = $('#edit-form').serialize();;
     if($('#edit-name').val()==""){
        message("Title fields are required.",false);
     }
    if( jsonObj == false){
      message("All Fields are required.",false);
    }else{
      $.ajax({ 
      url : '{{ route('task.updateedit') }}',
      type : "POST",
      data : jsonObj,
      headers: {
         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
     },
      success : function(data){
        message(data.message, data.status);

        if(data.status == true){
          $("#modal").hide();
          loadTable();
        }
      }
    });
  }
  });
});
</script>
