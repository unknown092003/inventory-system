const employeeFolder = document.querySelectorAll(".employee-folder");

employeeFolder.forEach((folder) => {
    folder.addEventListener("click", function () {
      location.href = "home/employee?id=" + this.dataset.empId;
    });
  });