function ValidateRegisterform() {
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const role = document.getElementById('role').value;
    if (!name || !email || !password || !role) {
        alert("Please fill in all fields");
        return false;
    }
    if (password.length < 6) {
        alert('Password must be at least 6 characters');
        return false;
    }
    alert(`Registration successful!\nName:${name}\nEmail:${email}\nRole:${role}`);
    return true;
}
function showDescriptionBox(show) {
    const box = document.getElementById('descriptionBox');
    if (show) {
        box.style.display = 'block';
    }
    else
        box.style.display = 'none';
}

function showBookConfirmation() {
    let specialist = document.getElementById("specialist")?.value;
    let date = document.getElementById("date")?.value;

    if (specialist && date) {
        alert(`Appointment booked with ${specialist.replace("_", " ")} on ${date}!`);
    } else {
        alert("Please select both a doctor and a date.");
    }
}

const specialists = {
    psychologist_karam: {
        name: 'Psy. Karam',
        specialization: 'Psychologist',
        bio: 'Licensed Clinical Psychologist specializing in child development and Autism Spectrum Disorder (ASD).'
    },
    spl_maya: {
        name: 'SPL Maya Haddad',
        specialization: 'Speech Therapist',
        bio: 'Certified speech-language therapist with experience supporting children with autism.'
    }
};

window.onload = function () {
    const specialistSelect = document.getElementById('specialist');

    if (specialistSelect) {
        const specialistInfo = document.createElement('div');
        specialistInfo.id = 'specialist-info';
        specialistSelect.insertAdjacentElement("afterend", specialistInfo);
    }
};

function showSpecialistInfo(selectedSpecialist) {
    const specialistInfo = document.getElementById('specialist-info');

    if (selectedSpecialist && specialists[selectedSpecialist]) {
        const doc = specialists[selectedSpecialist];
        specialistInfo.innerHTML = `
            <h3>${doc.name}</h3>
            <p><strong>Specialization:</strong> ${doc.specialization}</p>
            <p>${doc.bio}</p>
        `;
    } else if (specialistInfo) {
        specialistInfo.innerHTML = '';
    }
}

function confirmDelete() {
    return confirm("Are you sure you want to delete this appointment?");
}

function validateAppointmentDate(dateInput) {
    if (!dateInput.value) {
        dateInput.setCustomValidity('Please select a date');
        dateInput.reportValidity();
        return false;
    }

    const selectedDate = new Date(dateInput.value);
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    dateInput.setCustomValidity('');

    if (selectedDate < today) {
        dateInput.setCustomValidity('Cannot book appointments in the past');
        dateInput.reportValidity();
        return false;
    }

    if (selectedDate.getDay() === 0 || selectedDate.getDay() === 6) {
        dateInput.setCustomValidity('Clinic is closed on weekends');
        dateInput.reportValidity();
        return false;
    }

    return true;

}

function showSymptoms() {
    const age = document.getElementById("ageSelect").value;
    const symptomsBox = document.getElementById("symptomsBox");

    let symptoms = "";

    if (age === "1") {
        symptoms = `
            <ul>
                <li>Limited eye contact</li>
                <li>Rare smiling or facial expressions</li>
                <li>Limited response to sounds</li>
                <li>Reduced interest in people</li>
            </ul>
        `;
    }
    else if (age === "2") {
        symptoms = `
            <ul>
                 <li>Does not respond to name</li>
                <li>Limited babbling</li>
                <li>Little interest in social interaction</li>
                <li>Rarely imitates sounds or expressions</li>
            </ul>
        `;
    }
    else if (age === "3") {
        symptoms = `
            <ul>
                 <li>No pointing or waving</li>
                <li>Limited use of gestures</li>
                <li>Delayed speech sounds</li>
                <li>Lack of shared attention</li>
            </ul>
        `;
    }
    else if (age === "4") {
        symptoms = `
            <ul>
                  <li>Few or no meaningful words</li>
                <li>Does not follow simple instructions</li>
                <li>Repetitive behaviors</li>
                <li>Limited pretend play</li>
            </ul>
        `;
    }
    else if (age === '5') {
        symptoms = `
            <ul>
                <li>Very few or no meaningful, two-word phrases 
                (not including imitating or repeating)</li>
                 <li>Limited vocabulary</li>
                <li>Difficulty interacting with peers</li>
                <li>Repeats words without meaning</li>
                <li>Strong attachment to routines</li>
               
            </ul>
        `;
    }

    else if (age === '6') {
        symptoms = `
            <ul>
                <li>Difficulty forming sentences</li>
                <li>Limited social skills</li>
                <li>Repetitive movements</li>
                <li>Difficulty expressing emotions</li>
                <li>Avoidance of eye contact</li>
                <li>Unusual and intense reactions to sounds, smells, tastes, textures, lights and/or colors</li>
                <li>Restricted interests</li>
            </ul>
        `;
    }
    else {
        symptoms = "";
    }

    symptomsBox.innerHTML = symptoms;
}
function showDeleteConfirmation() {
    alert("Your appointment has been deleted.");
}

function showUpdateConfirmation() {
    alert("Your appointment has been updated")
}

function closeUpdateModal() {
    document.getElementById("update-modal").style.display = "none";
}

function openUpdateModal(appointmentId, doctor, date) {
    document.getElementById("update-modal").style.display = "block";
    document.getElementById("appointment_id_update").value = appointmentId;
    document.getElementById("doctor_update").value = doctor;
    document.getElementById("date_update").value = date;
}
function closeAllModals() {
    closeAddParentModal();
    closeAddSpecialistModal();
    closeUpdateParentModal();
    closeUpdateSpecialistModal();
}
function openAddParentModal() {
    closeAllModals();
    document.getElementById('add-parent-modal').style.display = 'block';
}
function closeAddParentModal() {
    document.getElementById('add-parent-modal').style.display = 'none';
}
function openAddSpecialistModal() {
    closeAllModals();
    document.getElementById('add-specialist-modal').style.display = 'block';
}
function closeAddSpecialistModal() {
    document.getElementById('add-specialist-modal').style.display = 'none';
}
function openUpdateParentModal() {
    closeAllModals();
    document.getElementById('update-parent-modal').style.display = 'block';
}
function closeUpdateParentModal() {
    document.getElementById('update-parent-modal').style.display = 'none';
}
function openUpdateSpecialistModal() {
    closeAllModals();
    document.getElementById('update-specialist-modal').style.display = 'block';

}
function closeUpdateSpecialistModal() {
    document.getElementById('update-specialist-modal').style.display = 'none';
}
function showDeclineMessage() {
    alert('The appointment is declined')
}
function showApproveMessage() {
    alert('The appointment is approved')
}
function volunteer() {
    alert('Thank you for volunteering!We will contact you soon');
}      