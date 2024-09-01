<?php

// Specify the file name or path
$file = 'data.txt';

// check if the file exists
if (file_exists($file)) {

    // Load the content from the file
    $content = file_get_contents($file);

    // enconding the file content
    $jsonData = json_encode($content);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta property="og:title" content="Calendar File" />
    <meta name="twitter:title" content="Calendar File" />
    <meta name="description" content="Mark your days and save" />
    <meta property="og:description" content="Mark your days and save" />
    <meta name="twitter:description" content="Mark your days and save" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <title>Calendar</title>
    <link rel="stylesheet" type="text/css" href="index.css" />
</head>

<body>

    <body>

        <div class="container">



            <div class="table">

                <div class="activeDate">
                    <button onclick="updateSelectedDate('decrease')">
                        <i class="ph ph-arrow-circle-left"></i>
                    </button>
                    <div class="title">
                        <i class="ph ph-calendar-dots"></i>
                        <p id="Month">
                        </p>
                    </div>
                    <button onclick="updateSelectedDate('increase')">
                        <i class="ph ph-arrow-circle-right"></i>
                    </button>
                </div>

                <table id="calendar" class="calendar">
                    <thead>
                        <tr>
                            <th>Sun</th>
                            <th>Mon</th>
                            <th>Tue</th>
                            <th>Wed</th>
                            <th>Thu</th>
                            <th>Fri</th>
                            <th>Sat</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

            <div class="buttons">
                <button id="save" onclick="saveSelectedDates()"><i class="ph ph-floppy-disk-back"></i></button>
                <button onclick="selectAllDates()"><i class="ph ph-selection-plus"></i></button>
                <button onclick="deselectAllDates()"><i class="ph ph-selection-slash"></i></button>
            </div>




        </div>


        <script>
            // Element references and default values
            const currentMonthElement = document.querySelector("#Month");
            let currentMonth = 9; // Default month (September)
            let currentYear = 2024; // Default year
            let selectedDatesByMonth = [];
            const activeMonthElement = document.querySelector('#Month');
            // Array to keep track of selected dates
            let selectedDates = [];

            // Array containing month names
            const monthNamesList = [
                "January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ];

            function createCalendar(year, month, markedDays = []) {
                const calendarBody = document.querySelector('#calendar tbody');
                calendarBody.innerHTML = ''; // Clear existing calendar content

                // Determine the number of days in the current month and the first day of the week
                const totalDaysInMonth = new Date(year, month + 1, 0).getDate();
                const firstWeekDayOfMonth = new Date(year, month, 1).getDay();
                let dayCounter = 1; // Counter to track the current day number

                // Loop to create the calendar rows and cells
                for (let rowIndex = 0; rowIndex < 6; rowIndex++) {
                    const tableRow = document.createElement('tr'); // Create a new row

                    // Loop through each day of the week (7 columns)
                    for (let columnIndex = 0; columnIndex < 7; columnIndex++) {
                        const dayCell = document.createElement('td'); // Create a new cell

                        // If it's the first row and the column index is less than the first weekday, add empty cells
                        if (rowIndex === 0 && columnIndex < firstWeekDayOfMonth) {
                            tableRow.appendChild(dayCell); // Empty cell
                        } else if (dayCounter <= totalDaysInMonth) {
                            dayCell.innerHTML = `<span class="dayNumber">${dayCounter}</span>`;

                            // Mark the day if it is in the markedDays array
                            if (markedDays.includes(dayCounter)) {
                                dayCell.querySelector('.dayNumber').classList.add('marked');
                            }

                            // Add event listener for marking/unmarking selected day
                            dayCell.addEventListener('click', function() {
                                const dayElement = this.querySelector('.dayNumber');

                                if (!dayElement) {
                                    console.error("Error: Day element (.dayNumber) not found inside the cell.");
                                    return;
                                }

                                const dayValue = Number(dayElement.innerHTML.trim());

                                // Toggle the 'marked' class to mark/unmark the selected day
                                const cellStatus = dayElement.classList.toggle('marked');

                                if (cellStatus) {
                                    updateSelectedDates(currentYear, currentMonth, dayValue);
                                } else {
                                    updateSelectedDates(currentYear, currentMonth, dayValue);
                                }
                            });

                            tableRow.appendChild(dayCell); // Add the day cell to the row
                            dayCounter++; // Increment the day counter
                        }
                    }

                    // Append the row to the calendar body
                    calendarBody.appendChild(tableRow);
                }


                activeMonthElement.innerHTML = `${monthNamesList[currentMonth]} ${currentYear}`;
            }


            // Handles the marking and unmarking of selected days
            function handleDayClick(dayElement) {
                const dayValue = Number(dayElement.innerHTML.trim()); // Get the numeric value of the day
                const isMarked = dayElement.classList.toggle('marked'); // Toggle the 'marked' class

                // Find or create the entry for the current month and year
                let monthData = selectedDates.find(item => item.year === currentYear && item.month === currentMonth);

                if (!monthData) {
                    monthData = {
                        year: currentYear,
                        month: currentMonth,
                        days: []
                    };
                    selectedDates.push(monthData);
                }

                if (isMarked) {
                    // Add the day to the days array if it is marked
                    if (!monthData.days.includes(dayValue)) {
                        monthData.days.push(dayValue);
                    }
                } else {
                    // Remove the day from the days array if it is unmarked
                    const dayIndex = monthData.days.indexOf(dayValue);
                    if (dayIndex > -1) {
                        monthData.days.splice(dayIndex, 1);
                    }
                }

                console.log('Updated selectedDates:', selectedDates); // Debugging: Log the current selected dates
            }


            // Updates the selected year and month based on the action ('increase', 'decrease', 'launch')
            function updateSelectedDates(year, month, day) {
                // Find or create the data structure for the selected dates
                let monthData = selectedDates.find(item => item.year === year && item.month === month);

                if (!monthData) {
                    // If no existing entry, create a new one
                    monthData = {
                        year: year,
                        month: month,
                        days: []
                    };
                    selectedDates.push(monthData);
                }

                // Toggle the day in the days array
                const dayIndex = monthData.days.indexOf(day);

                if (dayIndex > -1) {
                    // If day is already selected, remove it (unmark)
                    monthData.days.splice(dayIndex, 1);
                } else {
                    // If day is not selected, add it (mark)
                    monthData.days.push(day);
                }

                console.log('Updated selectedDates:', selectedDates); // Debugging: Log the current selected dates
            }


            function updateSelectedDate(action, day = null) {

                // Update year and month based on the action ("increase", "decrease", or default to current date)
                switch (action) {
                    case "increase": {
                        if (currentMonth >= 11) {
                            currentYear += 1;
                            currentMonth = 0;
                        } else {
                            currentMonth += 1;
                        }
                        break;
                    }
                    case "decrease": {
                        if (currentMonth <= 0) {
                            currentYear -= 1;
                            currentMonth = 11;
                        } else {
                            currentMonth -= 1;
                        }
                        break;
                    }
                    default: {
                        const today = new Date();
                        currentMonth = today.getMonth();
                        currentYear = today.getFullYear();
                        break;
                    }
                }

                // Update the displayed month and year in the UI
                activeMonthElement.innerHTML = `${monthNamesList[currentMonth]} ${currentYear}`;

                // Recreate the calendar for the updated year and month
                createCalendar(currentYear, currentMonth);
            }


            // Handles marking/unmarking and stores the selected dates for specific months
            function updateSelectedDates(year, month, day) {
                // Find the corresponding month and year in the selectedDatesByMonth array
                const monthData = selectedDatesByMonth.find(item => item.year === year && item.month === month);

                selectedDates.push(day)
                selectedDatesByMonth = []
                // If there is no entry for this month and year, create a new one
                selectedDatesByMonth.push({
                    year,
                    month,
                    days: selectedDates
                });

                console.log('Updated selectedDatesByMonth:', selectedDatesByMonth);
            }

            // Initializes the calendar on page load
            updateSelectedDate("launch");




            // Selects all available days in the current month of the calendar
            function selectAllDates() {


                selectedDates = []

                // Get all day cells in the calendar
                const calendarDays = document.querySelectorAll('#calendar tbody tr td');

                // Clear previously selected dates for the current month
                const currentMonthData = selectedDates.find(item => item.year === currentYear && item.month === currentMonth);
                
                // Loop through each day cell and mark it as selected
                calendarDays.forEach(dayCell => {
                    const dayElement = dayCell.querySelector('.dayNumber');

                    // If dayElement exists, mark it and update selectedDates
                    if (dayElement) {
                        // Add 'marked' class to the day element
                        dayElement.classList.add('marked');

                        // Add the day number to the selectedDates array for the current month
                        const dayValue = Number(dayElement.innerHTML.trim());
                        selectedDates.push(dayValue)
                        if (currentMonthData) {
                            currentMonthData.days.push(dayValue);
                        }
                    }
                });
                selectedDatesByMonth = []
                // If there is no entry for this month and year, create a new one
                selectedDatesByMonth.push({
                    year: currentYear,
                    month: currentMonth,
                    days: selectedDates
                });

                console.log('All dates selected for', {
                    year: currentYear,
                    month: monthNamesList[currentMonth]
                }, ':', selectedDates);
            }

            // Deselects all marked days in the current month of the calendar
            function deselectAllDates() {
                // Get all day cells in the calendar
                const calendarDays = document.querySelectorAll('#calendar tbody tr td');

                // Loop through each day cell and remove the 'marked' class
                calendarDays.forEach(dayCell => {
                    const dayElement = dayCell.querySelector('.dayNumber');

                    // If dayElement exists, unmark it
                    if (dayElement) {
                        // Remove 'marked' class from the day element
                        dayElement.classList.remove('marked');
                    }
                });

                // Clear the selected days for the current month
                const currentMonthData = selectedDates.find(item => item.year === currentYear && item.month === currentMonth);
                if (currentMonthData) {
                    currentMonthData.days = []; // Reset days for the current month
                } else {
                    // If no entry for the current month, create a new one with empty days
                    selectedDates.push({
                        year: currentYear,
                        month: currentMonth,
                        days: []
                    });
                }

                selectedDatesByMonth = []
                // If there is no entry for this month and year, create a new one
                selectedDatesByMonth.push({
                    year: currentYear,
                    month: currentMonth,
                    days: []
                });

                console.log('All dates deselected for', {
                    year: currentYear,
                    month: monthNamesList[currentMonth]
                }, ':', selectedDates);
            }


            // Saves the selected dates to a server-side file via an AJAX request
            function saveSelectedDates() {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'save.php');
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                // Prepare data in the new format
                const data = selectedDatesByMonth[0];

                // Convert data to JSON string
                const jsonData = JSON.stringify(data);

                xhr.onload = function() {
                    const messageElement = document.getElementById('message');
                    if (xhr.status === 200) {
                        showAlert('Data saved successfully', 'success');
                    } else {
                        showAlert('Failed to save data', 'failed');
                    }
                };

                xhr.send('data=' + encodeURIComponent(jsonData));
            }


            document.addEventListener("DOMContentLoaded", function() {
                fetch('data.txt')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok ' + response.statusText);
                        }
                        return response.text(); // Parse response as text
                    })
                    .then(text => {
                        try {
                            // Parse the single JSON line from the text
                            const data = JSON.parse(text);

                            console.log(data)

                            // Set the current year and month from the file data
                            currentYear = data.year;
                            currentMonth = data.month;

                            // Update the calendar display
                            currentMonthElement.innerHTML = `${data.month} ${data.year}`;
                            selectedDates = data.days;

                            // Create the calendar and pass the marked days
                            createCalendar(currentYear, currentMonth, selectedDates);
                        } catch (error) {
                            console.error('Error parsing JSON:', error);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching data:', error);
                    });
            });

            function showAlert(message, type) {
                // Create the alert element
                const alert = document.createElement('div');
                alert.classList.add('alert', `.${type}`);

                // Create progress bar container and bar
                const progressContainer = document.createElement('div');
                progressContainer.classList.add('progress-container');

                const progressBar = document.createElement('div');
                progressBar.classList.add('progress-bar');

                progressContainer.appendChild(progressBar);
                alert.appendChild(progressContainer);

                // Add the alert message
                const alertMessage = document.createElement('div');
                alertMessage.innerHTML = message;
                alert.appendChild(alertMessage);

                // Append alert to the body
                document.body.appendChild(alert);

                // Show the alert with animation
                setTimeout(() => alert.classList.add('show'), 100);

                // Start the progress bar animation
                setTimeout(() => progressBar.style.width = '100%', 100);

                // Remove the alert after 3 seconds
                setTimeout(() => {
                    alert.classList.remove('show');
                    setTimeout(() => alert.remove(), 500); // Remove after the transition ends
                }, 3000);
            }
        </script>
    </body>
</body>

</html>