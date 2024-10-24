document.getElementById('create-team-btn').addEventListener('click', function() {
    document.getElementById('form-container').classList.toggle('hidden');
});

// Add event listener for adding team members dynamically
document.getElementById('add-member-btn').addEventListener('click', function() {
    const teamMembersSection = document.getElementById('team-members-section');

    const memberRow = document.createElement('div');
    memberRow.className = 'form-row';

    memberRow.innerHTML = `
        <div class="form-group">
            <label for="member-name">Member Name</label>
            <input type="text" name="member_name[]" required>
        </div>
        <div class="form-group">
            <label for="roll-no">Roll No</label>
            <input type="text" name="roll_no[]" required>
        </div>
        <div class="form-group">
            <label for="member-role">Role</label>
            <select name="member_role[]" required>
                <option value="0" disabled selected>Select</option>
                <option value="Leader">Leader</option>
                <option value="Member">Member</option>
            </select>
        </div>
    `;
    teamMembersSection.appendChild(memberRow);

    const phoneEmailRow = document.createElement('div');
    phoneEmailRow.className = 'form-row';

    phoneEmailRow.innerHTML = `
        <div class="form-group">
            <label for="member-email">Email</label>
            <input type="email" name="member_email[]" required>
        </div>
        <div class="form-group">
            <label for="member-phone">Phone No</label>
            <input type="tel" name="member_phone[]" required>
        </div>
    `;
    teamMembersSection.appendChild(phoneEmailRow);
});
function viewTeam(teamId) {
    // Make an AJAX request to fetch the team details
    $.ajax({
        url: 'fetch_team.php',  // Ensure this file exists and handles the request
        type: 'POST',
        data: { id: teamId },
        success: function(response) {
            console.log(response); // Log the entire response object for debugging
            if (response.success) {
                let team = response.data;
                let detailsHtml = `<h3>${team.team_name}</h3>
                                   <p><strong>Department:</strong> ${team.department} &nbsp;&nbsp;&nbsp;
                                      <strong>Year:</strong> ${team.year} &nbsp;&nbsp;&nbsp;
                                      <strong>Size:</strong> ${team.team_size}</p>`;
        
                detailsHtml += `<h4>Members</h4>
                                <table border="1" cellpadding="10" cellspacing="0">
                                  <thead>
                                    <tr>
                                      <th>Member</th>
                                      <th>Roll No</th>
                                      <th>Email</th>
                                      <th>Phone</th>
                                      <th>Role</th>
                                    </tr>
                                  </thead>
                                  <tbody>`;
        
                team.members.forEach(member => {
                    detailsHtml += `<tr>
                                      <td>${member.member_name}</td>
                                      <td>${member.roll_no}</td>
                                      <td>${member.member_email}</td>
                                      <td>${member.member_phone}</td>
                                      <td>${member.member_role}</td>
                                    </tr>`;
                });
        
                detailsHtml += `</tbody></table>`;
        
                $('#team-details').html(detailsHtml);
                $('#team-modal').removeClass('hidden');
            } else {
                alert("Error fetching team details: " + (response.message || "Unknown error"));
            }
        },
               
        error: function() {
            alert("Error fetching team details.");
        }
    });
}

function closeModal() {
    $('#team-modal').addClass('hidden');
}


    // Function to edit team details
    function editTeam(teamId) {
        // Redirect to a page for editing the team details
        window.location.href = `edit_team.php?id=${teamId}`;
    }

    // Example of how to attach the viewTeam function to buttons (assuming you have buttons with data attributes)
    document.querySelectorAll('.view-team-button').forEach(button => {
        button.addEventListener('click', () => {
            const teamId = button.getAttribute('data-team-id');
            viewTeam(teamId);
        });
    });
    function closeModal() {
        document.getElementById('team-modal').classList.add('hidden');
    }
// Delete Team Functionality
function deleteTeam(teamId) {
    if (confirm('Are you sure you want to delete this team?')) {
        fetch(`delete_team.php?team_id=${teamId}`, { method: 'POST' })
            .then(response => {
                if (response.ok) {
                    alert('Team deleted successfully!');
                    location.reload(); // Reload the page to update the team list
                } else {
                    alert('Failed to delete team.');
                }
            })
            .catch(error => console.error('Error deleting team:', error));
    }
}

