@extends('layouts.app')
@section('content')
<h1>My Children</h1>
<p>Manage your children's profiles and track their therapy progress</p>
<nav>
    <ul>
        <li> <a href="{{ route('home') }}">Home</a></li>
        <li><a href="{{ route('parentprofile.dashboard') }}">Parent</a></li>
        <li>
            <form action="{{ route('logout') }}" method="POST">@csrf<button type="submit">Logout</button></form>
        </li>

    </ul>
</nav>
<form action="{{ route('child.store') }}" method="POST">
    @csrf
    <label for="childFirstName"> Child's FirstName:</label>
    <input type="text" id="childFirstName" name="childFirstName" placeholder="Child's First Name">
    <br><br>
    <label for="childLastName">Children LastName:</label>
    <input type="text" id="childLastName" name="childLastName" placeholder="Child's LastName">
    <br><br>
    <label for="dob">Date Of Birth:</label>
    <input type="date" id="dob" name="dob">
    <br><br>
    <label for="FatherName">Father Name:</label>
    <input type="text" id="FatherName" name="FatherName" placeholder="Father's Name">
    <br><br>
    <label for="MotherName">Mother Name:</label>
    <input type="text" id="MotherName" name="MotherName" placeholder="Mother's Name">
    <br><br>
    <label for="gender">Child's Gender</label>
    <select name="gender" id="gender">
        <option value="" disabled>Select your child's gender</option>
        <option value="male">Male</option>
        <option value="female">Female</option>
    </select>
    <br><br>
    <label for="autism-level">Autism Level</label>
    <select name="autism-level" id="autism-level">
        <option value="" disabled> Select Level</option>
        <option value="mild"> Mild</option>
        <option value="moderate"> Moderate</option>
        <option value="severe"> Severe</option>
    </select>
    <br><br>
    <label for="description">Description About your child's situation:</label>
    <br>
    <textarea name="description" id="description" placeholder="Describe the child's condition or needs">
    </textarea>
    <p>Does the child have another severe medical condition?</p>

    <label>
        <input type="radio" name="hasOtherDisease" value="yes" onclick="showDescriptionBox(true)">
        Yes
    </label>

    <label>
        <input type="radio" name="hasOtherDisease" value="no" onclick="showDescriptionBox(false)">
        No
    </label>
    <br><br>
    <div id="descriptionBox" style="display: none;">
        <label for="descriptionBox"> Please Describe the case of your child: </label>
        <br> <br>
        <textarea name="Box" id="Box" placeholder="Describe the medical condition"></textarea>
    </div>
    =======
    <label>
        <input type="radio" name="hasOtherDisease" value="no" onclick="showDescriptionBox(false)">
        No
    </label>
    <br><br>
    <div id="descriptionBox" style="display: none;">
        <label for="descriptionBox"> Please Describe the case of your child: </label>
        <br> <br>
        <textarea name="Box" id="Box" placeholder="Describe the medical condition"></textarea>
    </div>
    <button type="submit">Submit</button>

</form>

@endsection