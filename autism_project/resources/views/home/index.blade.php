@extends('layouts.app')

@section('content')
<section class="intro">
    <h1>Welcome to Autism World</h1>
    <p>Your trusted community for autism care and support</p>
    <br>
    <a href="{{ route('login') }}" class="btn">Login</a><br>
    <a href="{{ route('register') }}" class="btn btn-outline">Register</a>
</section>
<nav>
    <ul>
        <li><a href="#about">About Us</a></li>
        <li><a href="#services">Services</a></li>
        <li><a href="#volunteering">Volunteering</a></li>
        <li><a href="#team">Meet Our Team</a></li>
        <li><a href="#contact">Contact</a></li>
    </ul>
</nav>
<br>
<div class="image-row">
    <div class="image-box">
        <a href="{{ route('autism.dashboard') }}">
            <div class="image-text">What is Autism</div>
        </a>
        <img src="{{ asset('images/image.jpg') }}" alt="What is Autism" style="display:block; margin:auto;">
    </div>

    <div class="image-box">
        <a href="{{ route('autism.dashboard') }}">
            <div class="image-text">Symptoms of Autism</div>
        </a>
        <img src="{{ asset('images/photo.jpg') }}" alt="Symptoms of Autism" style="display:block; margin:auto;">
    </div>
</div>


<section id="about">
    <h2>About Us</h2>
    <br>
    <p>We provide resources, therapy guidance, and a supportive community for parents and specialists working with
        children on the autism spectrum.</p>
</section>

<section id="services">
    <h2>Our Services</h2>
    <br>
    <div class="services-container">
        <div class="card">
            <h3>Therapy Support</h3>
            <p>Access speech, occupational, and behavioral therapy guidance.</p>
        </div>
        <div class="card">
            <h3>Parent Community</h3>
            <p>Connect with other parents, share experiences, and learn tips.</p>
        </div>
        <div class="card">
            <h3>Specialist Directory</h3>
            <p>Find certified psychologists, speech therapists, and other experts.</p>
        </div>
    </div>
</section>
<section id="volunteering">
    <h2>Volunteering Opportunities</h2>

    <div class="card">
        <h3> Add New Opportunity</h3>
        <form action=" {{ route('volunteer.store') }}" method="POST" onsubmit="return volunteer()">
            @csrf

            <div>
                <label>Activity:</label>
                <input type="text" name="activity" placeholder="Organize events">
            </div>
            <div>
                <label>Name:</label>
                <input type="text" name="name" placeholder="Sarah Ahmad">
            </div>
            <div>
                <label>Location:</label>
                <input type="text" name="location" placeholder="Beirut">
            </div>
            <div>
                <label>Phone:</label>
                <input type="text" name="phone" placeholder="+961 123 4567">
            </div>
            <button type="submit" class="add-btn">Add</button>

        </form>
    </div>



</section>
<section id="team">
    <h2>Meet Our Team</h2>
    <br>
    <div class="team-container">
        <div class="card">
            <h3>Dr. Karam abdallah</h3>
            <p>Child Psychologist specializing in autism spectrum disorder.</p>
        </div>
        <div class="card">
            <h3>Maya Haddad</h3>
            <p>Speech Therapist helping children improve communication skills.</p>
        </div>

    </div>
</section>

<section id="contact">
    <h2>Contact Us</h2>
    <br>
    <p>Email: info@autismworld.com | Phone: +961 123 4567</p>
    <p>Address: Beirut, Lebanon</p>
</section>




@endsection