import { Modal } from "bootstrap";
import { get, post, del } from "../ajax";
import DataTable from "datatables.net";
import Swal from "sweetalert2";

window.addEventListener("DOMContentLoaded", function () {
  const viewPendingCheckupModal = new Modal(
    document.getElementById("checkupPendingModal")
  );
  const viewPrescriptionModal = new Modal(
    document.getElementById("viewPrescription")
  );
  const table = new DataTable("#patientCheckupTable", {
    serverSide: true,
    ajax: "/patient/checkupform/load",
    orderMulti: false,
    columns: [
      { data: "patient" },
      { data: "doctor" },
      { data: "hospital" },
      { data: "checkupDate" },
      { data: "prescription" },
      {
        sortable: false,
        data: (row) => `
                    <div class="d-flex flex-">
                        <a href="#">
                        <button class="ms-2 btn btn-outline-primary view-pending-btn" data-id="${row.id}" data-address="${row.patientAddress}" data-name="${row.patient}" data-age="${row.patientAge}" data-gender="${row.patientGender}" 
                        data-menstrualDate="${row.menstrualDate}" data-familyMember="${row.familyMember}" data-confineDate="${row.confineDate}" data-fetal="${row.fetal}" data-gravida="${row.gravida}" data-para="${row.para}" data-labaratory="${row.labaratory}"
                        data-urinalysis="${row.urinalysis}" data-bloodCount="${row.bloodCount}"  data-fecalysis="${row.fecalysis}" 
                        data-hospital="${row.hospital}" data-hospitalAddress="${row.hospitalAddress}" ">
                            Details
                        </button>
                        </a>
                          <a href="#">
                          <button class="ms-2 btn btn-outline-primary view-pdf-btn" data-id="${row.id}">PDF</button>
                        </a>
                          <div class="dropdown">
                            <i class="ms-2 bi bi-gear fs-4 text-primary" role="button" data-bs-toggle="dropdown"></i>

                            <ul class="dropdown-menu">
                            ${row.request ? `
                                          <li>
                                                <a class="dropdown-item requested-btn" href="#" data-id="${ row.id }">
                                                    <i class="mr-5 bi bi-check-square-fill"></i> Requested 
                                                </a>
                                          </li>

                                            ` : 
                                            `
                                  <li>
                                    <a class="dropdown-item request-checkup-btn" href="#" data-id="${ row.id }">
                                        <i class="bi bi-card-text"></i> Request 
                                    </a>
                                </li>
                            
                            `}
                            ${row.isPrescribed ? `
                                <li>
                                    <a class="dropdown-item view-prescription-btn" href="#" data-id="${ row.id }">
                                        <i class="bi bi-eye-fill"></i> View Prescription
                                    </a>
                                </li>
                            
                            ` : 
                            `
                                  <li>
                                    <a class="dropdown-item pending-prescription-btn" href="#" data-id="${ row.id }">
                                       <i class="bi bi-eye-slash"></i> Ongoing Prescription
                                    </a>
                                </li>
                          `
                        }
                            </ul>
                        </div>
        
                    </div>
                `,
      },
    ],
  });

  document
    .querySelector("#patientCheckupTable")
    .addEventListener("click", function (event) {
      const detailBtn = event.target.closest(".view-pending-btn");
      const viewBtn = event.target.closest(".view-prescription-btn")
      const requestCheckup = event.target.closest(".request-checkup-btn");
      const requested = event.target.closest(".requested-btn");
      const pendingPrescription = event.target.closest(".pending-prescription-btn");
      const viewPdf = event.target.closest(".view-pdf-btn");

      if (detailBtn) {
        const patientName = detailBtn.getAttribute("data-name");
        const patientAddress = detailBtn.getAttribute("data-address");
        const patientGender = detailBtn.getAttribute("data-gender");
        const patientAge = detailBtn.getAttribute("data-age");
        const patientMenstrualDate =
          detailBtn.getAttribute("data-menstrualDate");
        const patientFamilyMember = detailBtn.getAttribute("data-familyMember");
        const patientConfineDate = detailBtn.getAttribute("data-confineDate");
        const patientFetal = detailBtn.getAttribute("data-fetal");
        const patientGravida = detailBtn.getAttribute("data-gravida");
        const patientPara = detailBtn.getAttribute("data-para");
        const patientLabaratory = detailBtn.getAttribute("data-labaratory");
        const patientUrinalysis = detailBtn.getAttribute("data-urinalysis");
        const patientFecalysis = detailBtn.getAttribute("data-fecalysis");
        const hospitalName = detailBtn.getAttribute("data-hospital");
        const hospitalAddress = detailBtn.getAttribute("data-hospitalAddress");

        openViewAdmissionModal(
          viewPendingCheckupModal,
          patientName,
          patientAddress,
          patientGender,
          patientAge,
          patientMenstrualDate,
          patientFamilyMember,
          patientConfineDate,
          patientFetal,
          patientGravida,
          patientPara,
          patientLabaratory,
          patientUrinalysis,
          patientFecalysis,
          hospitalName,
          hospitalAddress
        );
      } else if(viewBtn) {
        const checkupId = viewBtn.getAttribute("data-id")

      get(`/patient/checkupform/prescription/${ checkupId }`)
      .then(response => response.json())
      .then(response => openPrescriptionModal(viewPrescriptionModal, response.prescriptionImage))

      } else if(requestCheckup) {
         const checkupId = requestCheckup.getAttribute("data-id")
            Swal.fire({
            title: "Do you want to request this Checkup Form?",
            text: "confirm if yes",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Confirm"
          }).then((result) => {

            if (result.isConfirmed) {
              Swal.fire({
                title: "Success!",
                text: "The form succesfully requested.",
                icon: "success"
              });
              get(`/patient/checkupform/${checkupId}/requested`)
                .then(response => {
                  if (response.ok) {
                    table.draw()
                  }
                })

            }
          })
      } else if(requested) {
        Swal.fire("You already have requested this form check your checkup request and get reference number!");
      } else if(pendingPrescription) {
        Swal.fire("Wait for the doctor prescription thank you!");
      } else if (viewPdf) {
        const checkup = viewPdf.getAttribute("data-id");
         window.open(`/patient/checkupform/${checkup}/pdf`, `_blank`);
       }


      

    });



  function openViewAdmissionModal(
    modal,
    name,
    address,
    gender,
    age,
    menstrualDate,
    familyMember,
    confineDate,
    fetal,
    gravida,
    para,
    labaratory,
    urinalysis,
    fecalysis,
    hName,
    hAddress
  ) {
    const patientName = modal._element.querySelector("#patientName");
    const patientAddress = modal._element.querySelector("#patientAddress");
    const patientGender = modal._element.querySelector("#patientGender");
    const patientAge = modal._element.querySelector("#patientAge");
    const patientMenstrualDate = modal._element.querySelector(
      "#patientMenstrualDate"
    );
    const patientFamilyMember = modal._element.querySelector(
      "#patientFamilyMember"
    );
    const patientConfineDate = modal._element.querySelector(
      "#patientConfineDate"
    );
    const patientFetal = modal._element.querySelector("#patientFetal");
    const patientGravida = modal._element.querySelector("#patientGravida");
    const patientPara = modal._element.querySelector("#patientPara");
    const patientLabaratory =
      modal._element.querySelector("#patientLabaratory");
    const patientUrinalysis =
      modal._element.querySelector("#patientUrinalysis");
    const patientFecalysis = modal._element.querySelector("#patientFecalysis");
    const hospitalName = modal._element.querySelector("#hospitalName");
    const hospitalAddress = modal._element.querySelector("#hospitalAddress");

    patientName.innerHTML = name;
    patientAddress.innerHTML = address;
    patientGender.innerHTML = gender;
    patientAge.innerHTML = age;
    patientMenstrualDate.innerHTML = menstrualDate;
    patientFamilyMember.innerHTML = familyMember;
    patientConfineDate.innerHTML = confineDate;
    patientFetal.innerHTML = fetal;
    patientGravida.innerHTML = gravida;
    patientPara.innerHTML = para;
    patientLabaratory.innerHTML = labaratory;
    patientUrinalysis.innerHTML = urinalysis;
    patientFecalysis.innerHTML = fecalysis;
    hospitalName.innerHTML = hName;
    hospitalAddress.innerHTML = hAddress;

    modal.show();
  }
  function openPrescriptionModal(modal, prescriptionImage) {

    modal._element.querySelector('#prescriptionImage').src = "/img/prescription/" +  prescriptionImage

    modal.show()
}



});
