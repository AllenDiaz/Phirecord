import { Modal } from "bootstrap";
import { get, post, del } from "../ajax";
import DataTable from "datatables.net";
import Swal from 'sweetalert2';

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

  const table = new DataTable("#patientReferredTable", {
    serverSide: true,
    ajax: "/hospital/refer/accepted/load",
    orderMulti: false,
    columns: [

      { data: "patient" },
      { data: "hospital" },
      { data: "referralCode" },
      { data: "createdAt" },
      {
        sortable: false,
        data: (row) => `
                    <div class="d-flex flex-">
                      <button type="submit" class="btn btn-outline-primary delete-refer-btn" data-id="${row.id}">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                          <div class="dropdown">
                            <i class="bi bi-envelope-paper fs-4  ms-2 text-primary" role="button" data-bs-toggle="dropdown"></i>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item show-admission-btn" href="/hospital/admissionform/referred/show/${row.patientId}" data-id="${row.patientId}">
                                        Admission Form Record
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item show-checkup-btn" href="/hospital/checkupform/referred/show/${row.patientId}" data-id="${row.patientId}">
                                        Checkup Form Record
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item show-certificate-btn" href="/hospital/medicalform/referred/show/${row.patientId}" data-id="${row.patientId}">
                                        Medical Cerficate Record
                                    </a>
                                </li>
                            </ul>
                        </div>
                          <div class="dropdown">
                            <i class="bi bi-plus fs-4  ms-2 text-primary" role="button" data-bs-toggle="dropdown"></i>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item add-admission-btn" href="#" data-id="${row.patientId}"  data-name="${row.patientName}" data-address="${row.patientAddress}" data-gender="${row.patientGender}">
                                        <i class="bi bi-plus"></i> Patient Admission
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item add-checkup-btn" href="#" data-id="${row.patientId}"
                                    data-name="${row.patientName}" data-address="${row.patientAddress}" data-gender="${row.patientGender}">
                                    <i class="bi bi-plus"></i> Patient Checkup
                                    </a>
                                    <a class="dropdown-item add-medical-btn" href="#" data-id="${row.patientId}"
                                    data-name="${row.patientName}" data-address="${row.patientAddress}" data-gender="${row.patientGender}">
                                    <i class="bi bi-plus"></i> Patient Medical Certificate
                                </li>
                            </ul>
                        </div>
                    </div>
                `,
      },
    ],
  });

  document.querySelector("#patientReferredTable").addEventListener("click", function (event) {
      const deleteBtn = event.target.closest(".delete-refer-btn");
      const acceptBtn = event.target.closest(".accept-refer-btn");
      const admissionBtn = event.target.closest(".add-admission-btn");
      const checkupBtn = event.target.closest(".add-checkup-btn");
      const medicalBtn = event.target.closest(".add-medical-btn");

       if (deleteBtn) {
        const referId = deleteBtn.getAttribute("data-id");
        Swal.fire({
          title: "Are you sure?",
          text: "The Referred Patient will be remove!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!",
        }).then((result) => {
          if (result.isConfirmed) {
            Swal.fire({
              title: "Deleted!",
              text: "Referred Patient has been removed.",
              icon: "success",
            });
            del(`/hospital/refer/reject/${referId}`).then((response) => {
              if (response.ok) {
                table.draw();
              }
            });
          }
        });
       } else if(acceptBtn) {
          const patientId  = acceptBtn.getAttribute("data-id")
        get(`/hospital/refer/accept/${patientId}`).then(response =>  {
          if(response.ok) {
            table.draw()
              Swal.fire({
              title: "Patient Approve!",
              text: "Succesfully approved!",
              icon: "success"
              });
          }
        })
       } else if(admissionBtn) {
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

       }else if(checkupBtn) {

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

       } else if(medicalBtn) {
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
       }

    })

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

