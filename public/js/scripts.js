// API Calls and DOM Manipulation Functions

// Fetch data from API and display it in the DOM
async function fetchData(apiUrl) {
    try {
        const response = await fetch(apiUrl);
        if (!response.ok) throw new Error('Network response was not ok');
        const data = await response.json();
        displayData(data);
    } catch (error) {
        console.error('Fetch Error: ', error);
    }
}

// Display fetched data in the DOM
function displayData(data) {
    const outputElement = document.getElementById('output');
    outputElement.innerHTML = ''; // Clear previous content
    data.forEach(item => {
        const div = document.createElement('div');
        div.textContent = JSON.stringify(item);
        outputElement.appendChild(div);
    });
}

// Add event listener to a button for interaction
function initialize() {
    const button = document.getElementById('fetch-button');
    button.addEventListener('click', () => {
        fetchData('https://api.example.com/data');
    });
}

// Initialize the application when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', initialize);