document.addEventListener('DOMContentLoaded', function () {
    // Fetch completed projects from the backend (PHP)
    fetch('get_completed_projects.php')
        .then(response => response.json())
        .then(data => {
            const completedProjectsContainer = document.getElementById('completed-projects');

            // Check if data is returned
            if (data.length === 0) {
                completedProjectsContainer.innerHTML = '<p>No completed projects found.</p>';
                return;
            }

            // Loop through the data and create HTML for each project
            data.forEach(project => {
                // Create the project card div
                const projectCard = document.createElement('div');
                projectCard.classList.add('project-card');

                // Add project name
                const projectName = document.createElement('h3');
                projectName.textContent = project.project_name;
                projectCard.appendChild(projectName);

                // Add project status
                const projectStatus = document.createElement('p');
                projectStatus.classList.add('status');
                projectStatus.textContent = `Status: ${project.status}`;
                projectCard.appendChild(projectStatus);

                // Add completion date
                const completionDate = document.createElement('p');
                completionDate.textContent = `Completion Date: ${project.completion_date}`;
                projectCard.appendChild(completionDate);

                // Append the card to the projects container
                completedProjectsContainer.appendChild(projectCard);
            });
        })
        .catch(error => {
            console.error('Error fetching completed projects:', error);
        });
});
