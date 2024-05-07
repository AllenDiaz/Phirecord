import { Modal } from "bootstrap"
import { get, post, del } from "../ajax"
import DataTable from "datatables.net"
import Swal from 'sweetalert2';


window.addEventListener('DOMContentLoaded', function () {
    const viewPatientModal = new Modal(document.getElementById('viewPatientModal'))

    const table = new DataTable('#hospitalPatientPendingTable', {
        serverSide: true,
        ajax: '/hospital/patient/pending/load',
        orderMulti: false,
        columns: [
            {
                sortable: false,
                data: "profileImage"
            },
            { data: "name" },
            { data: "email" },
            { data: "createdAt" },
            {
            sortable: false,
              data: (row) => `
                    <div class="d-flex gap-2">                  
                        <button type="button" class="btn btn-success accept-patient-btn" data-id="${row.id}">Accept</button>
                        <button type="button" class="btn btn-danger reject-patient-btn" data-id="${row.id}">Reject</button>
                        <button class="ms-2 btn btn-outline-primary view-patient-btn" data-id="${row.id}" data-image="${row.proofImage}" data-address="${row.address}">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                    </div>
                `,
            },
        ]
    });

    document.querySelector('#hospitalPatientPendingTable').addEventListener('click', function (event) {
        const viewBtn = event.target.closest('.view-patient-btn')
        const acceptBtn = event.target.closest('.accept-patient-btn')
        const deleteBtn = event.target.closest('.reject-patient-btn')

        if (viewBtn) {
            const viewId = viewBtn.getAttribute('data-id')
            const viewImage = viewBtn.getAttribute('data-image')
            const viewAdress = viewBtn.getAttribute('data-address')

            openViewPatientModal(viewPatientModal, viewId, viewImage, viewAdress)
        } else if (acceptBtn) {
        const patientId  = acceptBtn.getAttribute("data-id")
        get(`/hospital/patient/accept/${patientId}`).then(response =>  {
          if(response.ok) {
            table.draw()
              Swal.fire({
              title: "Patient Approve!",
              text: "Succesfully approved!",
              icon: "success"
              });
          }
        })
    } else if (deleteBtn) {
        const patientId  = deleteBtn.getAttribute("data-id")
                Swal.fire({
                title: "Are you sure?",
                text: "The Patient will be remove!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                 
              if (result.isConfirmed) {
                    Swal.fire({
                    title: "Deleted!",
                    text: "Your file has been deleted.",
                    icon: "success"
                    });
                  get(`/hospital/patient/reject/${patientId}`).then(response =>  {
                    if (response.ok) {
                        table.draw()
                    }
                  })

                }
                })
        }
        
    })

    function openViewPatientModal(modal, id, image, address) {
        const patientImage = modal._element.querySelector('.patient-image')
        const patientAddress = modal._element.querySelector('#patient-address')

        patientImage.src = "/img/patient/" + image
        patientAddress.innerHTML = address

        modal.show()
    }




}); 