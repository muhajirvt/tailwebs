@extends('layouts.app')

@section('content')
<div class="container">
    <button type="button" class="btn btn-success" style="float: right" onclick="showStudentPopup(this)">Add Student</button>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Sl.No</th>
            <th>Name</th>
            <th>Subject</th>
            <th>Mark</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
            @forelse($students as $key => $student)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>
                        <div>
                            <input class="form-control" id="student{{$student->id}}_name" type="text" value="{{$student->name}}">
                            <i class="fa fa-check" aria-hidden="true" style="float: right;margin-top: -25px;padding-right: 10px" onclick="updateStudent({{$student->id}},'name')"></i>
                        </div>
                    </td>
                    <td>
                        <div>
                            <input class="form-control" id="student{{$student->id}}_subject" type="text" value="{{$student->subject}}" readonly>
                            {{-- <i class="fa fa-check" aria-hidden="true" style="float: right;margin-top: -25px;padding-right: 10px" onclick="updateStudent({{$student->id}},'subject')></i> --}}
                        </div>
                    </td>
                    <td>
                        <div>
                            <input class="form-control" id="student{{$student->id}}_mark" type="number" value="{{$student->mark}}">
                            <i class="fa fa-check" aria-hidden="true" style="float: right;margin-top: -25px;padding-right: 10px" onclick="updateStudent({{$student->id}},'mark')"></i>
                        </div>
                    </td>
                    <td>
                        <button type="button" class="btn btn-warning" onclick="showStudentPopup(this)" data-id="{{$student->id}}" data-name="{{$student->name}}" data-subject="{{$student->subject}}" data-mark="{{$student->mark}}">Edit</button>
                        <a onclick='confirmDelete({{"$student->id"}})'>
                            <button type="button" class="btn btn-danger">Delete</button>
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" align="center">No students yet</td><tr>
            @endforelse
        </tbody>
    </table>
</div>

<div id="studentModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card-body">
                            <form method="POST" id="studentForm" action="{{ route('add.edit.student') }}">
                                @csrf
                                <input type="hidden" name="student_id" id="student_id" value="">
                                <div class="row mb-3">
                                    <label for="name" class="col-md-4 col-form-label text-md-end">Name</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="name" id="name">
                                        <span style="color:red" class="error-span" id="name-error"></span>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="subject" class="col-md-4 col-form-label text-md-end">Subject</label>
                                    <div class="col-md-6">
                                        <select class="form-control" name="subject" id="subject">
                                            @foreach ($subjects as $subject)
                                                <option>{{$subject}}</option>
                                            @endforeach
                                        </select>
                                        <span style="color:red" class="error-span" id="subject-error"></span>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="mark" class="col-md-4 col-form-label text-md-end">Mark</label>
                                    <div class="col-md-6">
                                        <input type="number" class="form-control" name="mark" id="mark">
                                        <span style="color:red" class="error-span" id="mark-error"></span>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-primary" style="float:right;margin:5px" onclick="addEditStudent()">Submit</button>
                                    <button type="button" class="btn btn-danger"  style="float:right;margin:5px" onclick="hideModal()">Close</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>
</div>
@endsection

@section('scripts')
    <script>
        function clearValues() {
            $('#studentForm').find('input[type=hidden], input[type=text], input[type=number]').val('');
        }

        function showModal() {
            $('#studentModal').modal('show');
        }

        function hideModal() {
            clearValues();
            $('#studentModal').modal('hide');
        }

        function showStudentPopup(obj) {
            var id = $(obj).attr('data-id');
            var modalTitle = "Add Student";
            if(id) {
              modalTitle = "Edit Student";
              $('#student_id').val(id);
              $('#name').val($(obj).attr('data-name'));
              $('#subject').val($(obj).attr('data-subject'));
              $('#mark').val($(obj).attr('data-mark'));
            }
            $('.modal-title').text(modalTitle);
            showModal();
        }

        function addEditStudent() {
            var formData = new FormData($('#studentForm')[0]);
            $('.error-span').empty(); 
            $.ajax({
                type: 'POST',
                url: $('#studentForm').attr('action'),
                data: formData,
                processData: false,
                contentType: false,
                success: function(response){
                    if(response.status == 0) {
                        $.each(response.message, function(fieldName,errorMessage) {
                            var caste = $('#'+fieldName+"-error");
                            $.each(errorMessage, function(key,error) {
                                caste.html(error);
                            });
                        });
                    } else {
                        toastr.success(response.message,"Success");
                        setTimeout(function(){
                            location.reload();
                        },500);
                    }
                }
            });
        }

        function updateStudent(studentId,fieldName) {
            $.ajax({
                type: 'PATCH',
                url: "{{route('update.student')}}",
                data: {studentId:studentId,fieldName:fieldName,value:$('#student'+studentId+'_'+fieldName).val(),_token:"{{csrf_token()}}"},
                success: function(response){
                    if(response.status == 0) {
                        toastr.error(response.message,"Error");
                    } else {
                        toastr.success(response.message,"Success");
                        setTimeout(function(){
                            location.reload();
                        },500);
                    }
                }
            });
        }

        function confirmDelete(studentId) {
            if (confirm("Are you sure you want to delete this student?")) {
                $.ajax({
                    type: 'get',
                    url: "{{ url('delete-student') }}/" + studentId,
                    success: function (response) {
                        if(response.status == 0) {
                            toastr.error(response.message,"Error");
                        } else {
                            toastr.success(response.message,"Success");
                            setTimeout(function(){
                                location.reload();
                            },500);
                        }
                    },
                });
            }
        }
    </script>
@endsection