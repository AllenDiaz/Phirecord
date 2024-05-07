import { Modal } from "bootstrap";
import { get, post, del } from "../ajax";
import DataTable from "datatables.net";
import Swal from "sweetalert2";

window.addEventListener("DOMContentLoaded", function () {
  const viewPendingCheckupModal = new Modal(
    document.getElementById("checkupPendingModal")
  );
  const uploadPrescriptionModal = new Modal(
    document.getElementById("prescriptionModal")
  );
  const table = new DataTable("#patientPendingPrescriptionTable", {
    serverSide: true,
    ajax: "/doctor/checkupform/pending/load",
    orderMulti: false,
    columns: [
      { data: "patient" },
      { data: "doctor" },
      { data: "hospital" },
      { data: "checkupDate" },
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
                         <button class="ms-2 btn btn-outline-primary add-prescription-btn" data-id="${row.id}" 
                         data-address="${row.patientAddress}" data-name="${row.patient}" data-age="${row.patientAge}" data-gender="${row.patientGender}" data-hospital="${row.hospital}" data-hospitalAddress="${row.hospitalAddress}" data-menstrualDate="${row.menstrualDate}" data-familyMember="${row.familyMember}" data-confineDate="${row.confineDate}" data-fetal="${row.fetal}" data-gravida="${row.gravida}" data-para="${row.para}" data-labaratory="${row.labaratory}"
                        data-urinalysis="${row.urinalysis}" data-bloodCount="${row.bloodCount}"  data-fecalysis="${row.fecalysis}" ">
                         <i class="bi bi-plus"></i>  Prescription
                         </button>
                        </a>
                    </div>
                `,
      },
    ],
  });

  document
    .querySelector("#patientPendingPrescriptionTable")
    .addEventListener("click", function (event) {
      const detailBtn = event.target.closest(".view-pending-btn");
      const uploadPrescription = event.target.closest(".add-prescription-btn")

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
      }else if(uploadPrescription) {
       const checkupId = uploadPrescription.getAttribute("data-id")
       const patientName = uploadPrescription.getAttribute("data-name");
       const patientAddress = uploadPrescription.getAttribute("data-address");
       const patientGender = uploadPrescription.getAttribute("data-gender");
       const patientAge = uploadPrescription.getAttribute("data-age");
       const hospitalName = uploadPrescription.getAttribute("data-hospital");
       const hospitalAddress = uploadPrescription.getAttribute("data-hospitalAddress");
        const patientMenstrualDate = uploadPrescription.getAttribute("data-menstrualDate");
        const patientFamilyMember = uploadPrescription.getAttribute("data-familyMember");
        const patientConfineDate = uploadPrescription.getAttribute("data-confineDate");
        const patientFetal = uploadPrescription.getAttribute("data-fetal");
        const patientGravida = uploadPrescription.getAttribute("data-gravida");
        const patientPara = uploadPrescription.getAttribute("data-para");
        const patientLabaratory = uploadPrescription.getAttribute("data-labaratory");
        const patientUrinalysis = uploadPrescription.getAttribute("data-urinalysis");
        const patientFecalysis = uploadPrescription.getAttribute("data-fecalysis");


        uploadPrescriptionModal._element
                              .querySelector('.upload-prescription-btn')
                              .setAttribute('data-id', checkupId)
        uploadPrescriptionModal._element
                              .querySelector('#patientName')
                              .innerHTML = patientName
        uploadPrescriptionModal._element
                              .querySelector('#patientGender')
                              .innerHTML = patientGender
        uploadPrescriptionModal._element
                              .querySelector('#patientAge')
                              .innerHTML = patientAge
        uploadPrescriptionModal._element
                              .querySelector('#patientAddress')
                              .innerHTML = patientAddress
        uploadPrescriptionModal._element
                              .querySelector('#hospitalName')
                              .innerHTML = hospitalName
        uploadPrescriptionModal._element
                              .querySelector('#hospitalAddress')
                              .innerHTML = hospitalAddress
        uploadPrescriptionModal._element
                              .querySelector('#patientMenstrualDate')
                              .innerHTML = patientMenstrualDate
        uploadPrescriptionModal._element
                              .querySelector('#patientFamilyMember')
                              .innerHTML = patientFamilyMember
        uploadPrescriptionModal._element
                              .querySelector('#patientConfineDate')
                              .innerHTML = patientConfineDate
        uploadPrescriptionModal._element
                              .querySelector('#patientFetal')
                              .innerHTML = patientFetal
        uploadPrescriptionModal._element
                              .querySelector('#patientGravida')
                              .innerHTML = patientGravida
        uploadPrescriptionModal._element
                              .querySelector('#patientPara')
                              .innerHTML = patientPara
        uploadPrescriptionModal._element
                              .querySelector('#patientLabaratory')
                              .innerHTML = patientLabaratory
        uploadPrescriptionModal._element
                              .querySelector('#patientUrinalysis')
                              .innerHTML = patientUrinalysis
        uploadPrescriptionModal._element
                              .querySelector('#patientFecalysis')
                              .innerHTML = patientFecalysis
                              
                              uploadPrescriptionModal.show()

      }

      
    document.querySelector('.upload-prescription-btn').addEventListener('click', function (event) {
        const prenatalCheckup = event.currentTarget.getAttribute('data-id')
        const formData      = new FormData()
        const files         = uploadPrescriptionModal._element.querySelector('input[type="file"]').files

        for (let i = 0; i < files.length; i++) {
            formData.append('prescription', files[i])
        }

        post(`/doctor/checkupform/${ prenatalCheckup }/prescription`, formData, uploadPrescriptionModal._element)
            .then(response => {
                if (response.ok) {
                    table.draw()
                    uploadPrescriptionModal.hide()
                    Swal.fire({
                    title: "Prescription Added!",
                    text: "Succesfully!",
                    icon: "success"
                    });
                      }
            })
    })


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
});
