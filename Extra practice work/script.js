const search = document.getElementById("search");
const studentsContainer = document.getElementById("students");
let students = [];

function displayStudents(list) {
    studentsContainer.innerHTML = list.map(student => `
        <div>
            <h3>${student.name}</h3>
            <p>Course: ${student.course}</p>
            <p>Marks: ${student.marks}</p>
        </div>
    `).join("");
}

async function loadStudents() {
    try {
        students = await fetch("student.json").then(response => response.json());
        displayStudents(students);
    } catch (error) {
        console.error("Unable to load student data:", error);
        studentsContainer.innerHTML = "<p>Unable to load student data.</p>";
    }
}

search.addEventListener("input", () => {
    const text = search.value.toLowerCase();
    displayStudents(students.filter(student => student.name.toLowerCase().includes(text)));
});

loadStudents();