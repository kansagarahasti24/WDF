const searchBox = document.getElementById("courseSearch");
const courseList = document.getElementById("courseList");
let courses = [];

function showCourses(list) {
    courseList.innerHTML = list.map((course, index) => `
        <p>Subject ${index + 1} - ${course}</p>
    `).join("");
}

async function loadCourses() {
    try {
        courses = await fetch("courses.json").then(response => response.json());
        showCourses(courses);
    } catch (error) {
        courseList.innerHTML = "<p>Unable to load courses.</p>";
    }
}

searchBox.addEventListener("input", () => {
    const searchText = searchBox.value.toLowerCase();
    showCourses(courses.filter(course => course.toLowerCase().includes(searchText)));
});

loadCourses();