import { Modal } from "bootstrap";
import { get, post, del } from "../ajax";
import DataTable from "datatables.net";
import Swal from "sweetalert2";

window.addEventListener("DOMContentLoaded", function () {
  const viewPendingCheckupModal = new Modal(
    document.getElementById("checkupPendingModal")
  );
  const table = new DataTable("#patientRequestCheckupTable", {
    serverSide: true,
    ajax: "/patient/checkupform/request/load",
    orderMulti: false,
    columns: [
      { data: "patient" },
      { data: "doctor" },
      { data: "hospital" },
      { data: "referenceCode" },
      { data: "requestDate" },
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
                        <button class="ms-2 btn btn-outline-primary download-reference-btn" data-id="${row.id}">Print Reference Number</button>
        
                    </div>
                `,
      },
    ],
  });

  document
    .querySelector("#patientRequestCheckupTable")
    .addEventListener("click", function (event) {
      
      const detailBtn = event.target.closest(".view-pending-btn");
      const downloadReference = event.target.closest(".download-reference-btn");

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
      } else if(downloadReference) {
      const checkupId = downloadReference.getAttribute("data-id")
      window.open(`/patient/checkupform/${checkupId}/referral`, `_blank`);
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




});
