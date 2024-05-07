import { Modal } from "bootstrap";
import { get, post, del } from "../ajax";
import DataTable from "datatables.net";
import Swal from 'sweetalert2';

window.addEventListener("DOMContentLoaded", function () {
const editMedicalModal = new Modal(document.getElementById("editMedicalModal"))
  const table = new DataTable("#patienMedicalTable", {
    serverSide: true,
    ajax: "/hospital/medicalform/load",
    orderMulti: false,
    columns: [

      { data: "patient" },
      { data: "doctor" },
      { data: "hospital" },
      { data: "certificateDate" },
      {
        sortable: false,
        data: (row) => `
                    <div class="d-flex flex-">

                        <a href="/hospital/medicalform/pdf/${row.id}" target="_blank">
                        <button class="ms-2 btn btn-outline-primary view-doctor-btn" data-id="${row.id}" >
                            <i class="bi bi-filetype-pdf"></i> PDF 
                        </button>
                        </a>

                      ${row.ownRecord ? `  
                        <div class="dropdown">
                            <i class="ms-2 bi bi-gear text-primary fs-4" role="button" data-bs-toggle="dropdown"></i>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item edit-medical-btn" href="#" data-id="${ row.id }">
                                        <i class="bi bi-pencil-fill"></i> Edit
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item delete-medical-btn" href="#" data-id="${ row.id }">
                                        <i class="bi bi-trash3-fill"></i> Delete
                                    </a>
                                </li>
                            </ul>
                        </div>` : 
                        `<div class="dropdown">
                            <i class="ms-2 bi bi-gear text-primary fs-4" role="button" data-bs-toggle="dropdown"></i>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item edit-notice-btn text-danger" href="#" data-id="${ row.id }">
                                       <i class="bi bi-ban"></i> Edit
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item delete-notice-btn text-danger" href="#" data-id="${ row.id }">
                                       <i class="bi bi-ban"></i> Delete
                                    </a>
                                </li>
                            </ul>
                        </div>`}
                    </div>
                `,
      },
    ],
  });

  document.querySelector("#patienMedicalTable").addEventListener("click", function (event) {
    const editBtn = event.target.closest(".edit-medical-btn")
    const deleteBtn = event.target.closest('.delete-medical-btn')
    const editNoticeBtn = event.target.closest('.edit-notice-btn')
    const deleteNoticeBtn = event.target.closest('.delete-notice-btn')


    if(editBtn) {
        const medicalId = editBtn.getAttribute('data-id')
        get(`/hospital/medicalform/${medicalId}/edit`)
                .then(response => response.json())
                .then(response => openEditMedicalModal(editMedicalModal, response))
    }else if(editNoticeBtn) {
           Swal.fire("You are not allowed for editing this medical form!");

    } else if(deleteNoticeBtn) {
           Swal.fire("You are not allowed to delete this medical form!");
    } else if(deleteBtn) {
        const medicalId = deleteBtn.getAttribute('data-id')

          Swal.fire({
          title: "Are you sure?",
          text: "The medical certificate record will be remove!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!",
        }).then((result) => {
          if (result.isConfirmed) {
            Swal.fire({
              title: "Deleted!",
              text: "medical certificate has been deleted.",
              icon: "success",
            });
            del(`/hospital/medicalform/${medicalId}`).then((response) => {
              if (response.ok) {
                table.draw();
              }
            });
          }
        });
    }


    })


    document.querySelector('.save-medical-btn').addEventListener('click', function (event) {
        const medicalId = event.currentTarget.getAttribute('data-id')
        post(`/hospital/medicalform/${ medicalId }`, getMedicalFormData(editMedicalModal), editMedicalModal._element)
            .then(response => {
                if (response.ok) {
                    table.draw()
                    editMedicalModal.hide()
                    Swal.fire({
                    title: "Medical Form Updated!",
                    text: "Success",
                    icon: "success"
                    });
                }
            })
    })

});
function getMedicalFormData(modal) {
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


function openEditMedicalModal(modal, {id, hospitalName, patientName, patientGender, patientAddress, patientAge, ...data}) {
    for (let name in data) {
        const nameInput = modal._element.querySelector(`[name="${ name }"]`)

        nameInput.value = data[name]
    }
    modal._element.querySelector('.hospital-name').innerHTML = hospitalName
    modal._element.querySelector('#patientName').innerHTML = patientName
    modal._element.querySelector('#patientGender').innerHTML = patientGender
    modal._element.querySelector('#patientAddress').innerHTML = patientAddress
    modal._element.querySelector('#patientAge').innerHTML = patientAge

    modal._element.querySelector('.save-medical-btn').setAttribute('data-id', id)

    modal.show()
  }