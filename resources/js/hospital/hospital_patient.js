import { Modal } from "bootstrap";
import { get, post, del } from "../ajax";
import DataTable from "datatables.net";
import Swal from "sweetalert2";

window.addEventListener("DOMContentLoaded", function () {
  const viewPatientModal = new Modal(
    document.getElementById("viewPatientModal")
  );
  const viewRegisterAdmissionModal = new Modal(
    document.getElementById("registerAdmissionModal")
  );
  const viewCheckupModal = new Modal(
    document.getElementById("registerCheckupModal")
  );
  const viewMedicalModal = new Modal(
    document.getElementById("registerMedicalModal")
  );
  const viewReferralModal = new Modal(
    document.getElementById("patientReferralModal")
  );
  const table = new DataTable("#hospitalPatientTable", {
    serverSide: true,
    ajax: "/hospital/patient/load",
    orderMulti: false,
    columns: [
      {
        sortable: false,
        data: "profileImage",
      },
      { data: "name" },
      { data: "email" },
      { data: "gender" },
      { data: "approveAt" },
      {
        sortable: false,
        data: (row) => `
                    <div class="d-flex flex-">
                        <button type="submit" class="btn btn-outline-primary delete-patient-btn" data-id="${row.id}">
                        <i class="bi bi-trash"></i> 
                        </button>
                        <button class="ms-2 btn btn-outline-primary view-patient-btn" data-id="${row.id}" data-image="${row.proofImage}" data-address="${row.address}">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                          <div class="dropdown">
                            <i class="bi bi-envelope-paper fs-4  ms-2 text-primary" role="button" data-bs-toggle="dropdown"></i>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item show-admission-btn" href="/hospital/admissionform/show/${row.id}" data-id="${row.id}">
                                        Admission Form Record
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item show-checkup-btn" href="/hospital/checkupform/show/${row.id}" data-id="${row.id}">
                                        Checkup Form Record
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item show-certificate-btn" href="/hospital/medicalform/show/${row.id}" data-id="${row.id}">
                                        Medical Cerficate Record
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="dropdown">
                            <i class="bi bi-plus fs-4  ms-2 text-primary" role="button" data-bs-toggle="dropdown"></i>

                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item add-admission-btn" href="#" data-id="${row.id}"  data-name="${row.name}" data-address="${row.address}" data-gender="${row.gender}">
                                        <i class="bi bi-plus"></i> Patient Admission
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item add-checkup-btn" href="#" data-id="${row.id}"
                                    data-name="${row.name}" data-address="${row.address}" data-gender="${row.gender}">
                                    <i class="bi bi-plus"></i> Patient Checkup
                                    </a>
                                    <a class="dropdown-item add-medical-btn" href="#" data-id="${row.id}"
                                    data-name="${row.name}" data-address="${row.address}" data-gender="${row.gender}">
                                    <i class="bi bi-plus"></i> Patient Medical Certificate
                                    </a>
                                    <a class="dropdown-item add-refer-btn" href="#" data-id="${row.id}"
                                    data-name="${row.name}" data-address="${row.address}" data-gender="${row.gender}">
                                    <i class="bi bi-plus"></i> Patient Refferal 
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                `,
      },
    ],
  });

  document
    .querySelector("#hospitalPatientTable")
    .addEventListener("click", function (event) {
      const viewBtn = event.target.closest(".view-patient-btn");
      const deleteBtn = event.target.closest(".delete-patient-btn");
      const admissionBtn = event.target.closest(".add-admission-btn");
      const checkupBtn = event.target.closest(".add-checkup-btn");
      const medicalBtn = event.target.closest(".add-medical-btn");
      const referralBtn = event.target.closest(".add-refer-btn");

      if (viewBtn) {
        const viewId = viewBtn.getAttribute("data-id");
        const viewImage = viewBtn.getAttribute("data-image");
        const viewAdress = viewBtn.getAttribute("data-address");

        openViewPatientModal(viewPatientModal, viewId, viewImage, viewAdress);
      } else if (admissionBtn) {
        const patientId = admissionBtn.getAttribute("data-id");
        const patientName = admissionBtn.getAttribute("data-name");
        const patientAddress = admissionBtn.getAttribute("data-address");
        const patientGender = admissionBtn.getAttribute("data-gender");
        openViewAdmissionModal(
          viewRegisterAdmissionModal,
          patientId,
          patientName,
          patientAddress,
          patientGender
        );
        document
          .querySelector(".create-admission-btn")
          .addEventListener("click", function (event) {
            post(
              `/hospital/admissionform`,
              getAdmissionFormData(viewRegisterAdmissionModal),
              viewRegisterAdmissionModal._element
            ).then((response) => {
              if (response.ok) {
                viewRegisterAdmissionModal.hide();
                async function successAdd() {
                  // Simulate waiting for a condition (e.g., user input, API response, etc.)
                  await new Promise((resolve) => setTimeout(resolve, 3000)); // Wait for 2 seconds (adjust as needed)

                  // Once the condition is met, execute your code here
                  Swal.fire({
                    title: "Success!",
                    text: "The Admission Form was Added!",
                    icon: "success",
                  });
                  location.reload();
                  // Your code goes here
                }
                successAdd();
              }
            });
          });
      } else if (checkupBtn) {
        const patientId = checkupBtn.getAttribute("data-id");
        const patientName = checkupBtn.getAttribute("data-name");
        const patientAddress = checkupBtn.getAttribute("data-address");
        const patientGender = checkupBtn.getAttribute("data-gender");

        openViewAdmissionModal(
          viewCheckupModal,
          patientId,
          patientName,
          patientAddress,
          patientGender
        );

        document
          .querySelector(".create-checkup-btn")
          .addEventListener("click", function (event) {
            post(
              `/hospital/checkupform`,
              getAdmissionFormData(viewCheckupModal),
              viewCheckupModal._element
            ).then((response) => {
              if (response.ok) {
                viewCheckupModal.hide();
                async function successAdd() {
                  // Simulate waiting for a condition (e.g., user input, API response, etc.)
                  await new Promise((resolve) => setTimeout(resolve, 3000)); // Wait for 2 seconds (adjust as needed)

                  // Once the condition is met, execute your code here
                  Swal.fire({
                    title: "Success!",
                    text: "The Checkup Form was Added!",
                    icon: "success",
                  });
                  location.reload();
                  // Your code goes here
                }
                successAdd();
              }
            });
          });
      } else if (medicalBtn) {
        const patientId = medicalBtn.getAttribute("data-id");
        const patientName = medicalBtn.getAttribute("data-name");
        const patientAddress = medicalBtn.getAttribute("data-address");
        const patientGender = medicalBtn.getAttribute("data-gender");

        openViewAdmissionModal(
          viewMedicalModal,
          patientId,
          patientName,
          patientAddress,
          patientGender
        );

        document
          .querySelector(".create-medical-btn")
          .addEventListener("click", function (event) {
            post(
              `/hospital/medicalform`,
              getAdmissionFormData(viewMedicalModal),
              viewMedicalModal._element
            ).then((response) => {
              if (response.ok) {
                viewMedicalModal.hide();
                async function successAdd() {
                  // Simulate waiting for a condition (e.g., user input, API response, etc.)
                  await new Promise((resolve) => setTimeout(resolve, 3000)); // Wait for 2 seconds (adjust as needed)

                  // Once the condition is met, execute your code here
                  Swal.fire({
                    title: "Success!",
                    text: "The Medical Certificate was Added!",
                    icon: "success",
                  });
                  location.reload();
                  // Your code goes here
                }
                successAdd();
              }
            });
          });
      } else if (deleteBtn) {
        const patientId = deleteBtn.getAttribute("data-id");
        Swal.fire({
          title: "Are you sure?",
          text: "The Patient will be remove!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!",
        }).then((result) => {
          if (result.isConfirmed) {
            Swal.fire({
              title: "Deleted!",
              text: "Your file has been deleted.",
              icon: "success",
            });
            get(`/hospital/patient/archive/${patientId}`).then((response) => {
              if (response.ok) {
                table.draw();
              }
            });
          }
        });
      } else if (referralBtn) {
        const patientId = referralBtn.getAttribute("data-id");
        const patientName = referralBtn.getAttribute("data-name");
        const patientAddress = referralBtn.getAttribute("data-address");
        const patientGender = referralBtn.getAttribute("data-gender");

        openViewAdmissionModal(
          viewReferralModal,
          patientId,
          patientName,
          patientAddress,
          patientGender
        );
        document
          .querySelector(".refer-patient-btn")
          .addEventListener("click", function (event) {
            post(
              `/hospital/refer`,
              getAdmissionFormData(viewReferralModal),
              viewReferralModal._element
            ).then((response) => {
              if (response.ok) {
                viewReferralModal.hide();
                async function successAdd() {
                  // Simulate waiting for a condition (e.g., user input, API response, etc.)
                  await new Promise((resolve) => setTimeout(resolve, 3000)); // Wait for 2 seconds (adjust as needed)

                  // Once the condition is met, execute your code here
                  Swal.fire({
                    title: "Success!",
                    text: "The Patient Succesfully Refer",
                    icon: "success",
                  });
                  location.reload();
                  // Your code goes here
                }
                successAdd();
              }
            });
          });
      }
    });

  function openViewPatientModal(modal, id, image, address) {
    const patientImage = modal._element.querySelector(".patient-image");
    const patientAddress = modal._element.querySelector("#patient-address");

    patientImage.src = "/img/patient/" + image;
    patientAddress.innerHTML = address;

    modal.show();
  }
});

function openViewAdmissionModal(modal, id, name, address, gender) {
  const patientId = modal._element.querySelector("#patient");
  const patientName = modal._element.querySelector("#patientName");
  const patientAddress = modal._element.querySelector("#patientAddress");
  const patientGender = modal._element.querySelector("#patientGender");

  patientId.value = id;
  patientName.innerHTML = name;
  patientAddress.innerHTML = address;
  patientGender.innerHTML = gender;

  modal.show();
}

function getAdmissionFormData(modal) {
  let data = [];
  data = [];
  const fields = [
    ...modal._element.getElementsByTagName("input"),
    ...modal._element.getElementsByTagName("select"),
    ...modal._element.getElementsByTagName("textarea"),
  ];

  fields.forEach((select) => {
    data[select.name] = select.value;
  });

  return data;
}

