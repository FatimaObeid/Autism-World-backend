@extends('layouts.app')
@section('content')
<header>
<h1>Welcome, {{ Auth::user()->name }}</h1>
<nav>
<ul>
<li><a href="{{ route('home') }}">Home</a></li>
<li><a href="#manage-specialists">Manage Specialists</a></li>
<li><a href="#manage-parents">Manage Parents</a></li>
<li>
<form method="POST" action="{{ route('logout') }}">
@csrf
<button type="submit">Logout</button>
</form>
</li>
</ul>
</nav>
</header>

<section id="manage-specialists">
<h2>Manage Specialists</h2>
<button type="button" onclick="openAddSpecialistModal()">Add Specialist</button>
<table>
<tr>
<th>Name</th>
<th>Specialization</th>
<th>Actions</th>
</tr>
@foreach ($specialists as $specialist)
<tr>
<td>{{ $specialist->user->name }}</td>
<td>{{ $specialist->specialization }}</td>
<td>
<button type="button"
onclick="openUpdateSpecialistrModal(
'{{ $specialist->id }}',
'{{ $specialist->user->name }}',
'{{ $specialist->user->email }}',
'{{ $specialist->specialization }}'
)">
Edit
</button>
<form method="POST" action="{{ route('admin.specialists.delete',

$specialist->id) }}" style="display:inline;">

@csrf
@method('DELETE')
<button type="submit">Delete</button>
</form>
</td>
</tr>
@endforeach
</table>
</section>

<section id="manage-parents">
<h2>Manage Parents</h2>
<button type="button" onclick="openAddParentModal()">Add Parent</button>
<table>
<tr>
<th>Name</th>
<th>Email</th>
<th>Actions</th>
</tr>
@foreach ($parents as $parent)
<tr>
<td>{{ $parentprofile->user->name }}</td>
<td>{{ $parentprofile->user->email }}</td>
<td>
    <button type="button"
onclick="openUpdateparentModal(
'{{ $parentprofile->id }}',
'{{ $parentprofile->user->name }}',
'{{ $parentprofile->user->email }}'
)">
Edit
</button>
<form method="POST" action="{{ route('admin.parents.delete',

$parent->id) }}" style="display:inline;">

@csrf
@method('DELETE')
<button type="submit">Delete</button>
</form>
</td>
</tr>
@endforeach
</table>
</section>

{{-- ===================== ADD specialist MODAL ===================== --}}
<div id="add-specialist-modal" class="modal-content" style="display:none;">
<h2>Add specialist</h2>
<form method="POST" action="{{ route('admin.specialists.save') }}">
@csrf
<label>Full Name</label>
<input type="text" name="name" required>
<label>Email</label>
<input type="email" name="email" required>
<label>Password</label>
<input type="password" name="password" required>
<label>Specialization</label>
<input type="text" name="specialization" required>
<button type="submit">Add specialist</button>
<button type="button" onclick="closeAddSpecialistModal()">Close</button>
</form>
</div>

<div id="update-specialist-modal" class="modal-content" style="display:none;">
<h2>Update specialist</h2>
<form method="POST" id="update-specialist-form" data-base-url="/admin/specialists">
@csrf
@method('PUT')
<label>Full Name</label>
<input type="text" name="name" id="specialist-name-update" required>
<label>Email</label>
<input type="email" name="email" id="specialist-email-update" required>
<label>Specialization</label>

<input type="text" name="specialization" id="specialist-specialization-
update" required>

<button type="submit">Update</button>
<button type="button" onclick="closeUpdatespecialistModal()">Close</button>
</form>
</div>

<div id="add-parent-modal" class="modal-content" style="display:none;">
<h2>Add parent</h2>
<form method="POST" action="{{ route('admin.parents.save') }}">
@csrf
<label>Full Name</label>
<input type="text" name="name" required>
<label>Email</label>
<input type="email" name="email" required>
<label>Password</label>
<input type="password" name="password" required>
<button type="submit">Add parent</button>
<button type="button" onclick="closeAddparentModal()">Close</button>
</form>
</div>

<div id="update-parent-modal" class="modal-content" style="display:none;">
<h2>Update parent</h2>
<form method="POST" id="update-parent-form" data-base-url="/admin/parents">
@csrf
@method('PUT')
<label>Full Name</label>
<input type="text" name="name" id="parent-name-update" required>
<label>Email</label>
<input type="email" name="email" id="parent-email-update" required>
<button type="submit">Update</button>
<button type="button" onclick="closeUpdateparentModal()">Close</button>
</form>
</div>
@endsection