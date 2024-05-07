import { Modal } from "bootstrap"
import { get, post, del } from "../ajax"
import DataTable from "datatables.net"
import Swal from 'sweetalert2';


window.addEventListener('DOMContentLoaded', function () {
    const viewDoctorModal = new Modal(document.getElementById('viewDoctorModal'))

    const table = new DataTable('#hospitalDoctorPendingTable', {
        serverSide: true,
        ajax: '/hospital/doctor/pending/load',
        orderMulti: false,
        columns: [
            {
                sortable: false,
                data: "profileImage"
            },
            { data: "name" },
            { data: "email" },
            { data: "address" },
            { data: "createdAt" },
        {
        sortable: false,
        data: (row) => `
                    <div class="d-flex gap-2">                  
                        <button type="button" class="btn btn-success accept-doctor-btn" data-id="${row.id}">Accept</button>
                        <button type="button" class="btn btn-danger reject-doctor-btn" data-id="${row.id}">Reject</button>
                        <button class="ms-2 btn btn-outline-primary view-doctor-btn" data-id="${row.id}" data-image="${row.proofImage}" data-address="${row.address}">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                    </div>
                `,
        },
        ]
    });

    document.querySelector('#hospitalDoctorPendingTable').addEventListener('click', function (event) {
      const viewBtn = event.target.closest(".view-doctor-btn")
      const acceptBtn = event.target.closest('.accept-doctor-btn')
      const rejectBtn = event.target.closest('.reject-doctor-btn')

      if (viewBtn) {
        const viewId = viewBtn.getAttribute("data-id");
        const viewImage = viewBtn.getAttribute("data-image");
        const viewAdress = viewBtn.getAttribute("data-address");
        openViewDoctorModal(viewDoctorModal, viewImage, viewAdress);
      
      } else if (acceptBtn) {
        const doctorId  = acceptBtn.getAttribute("data-id")
        get(`/hospital/doctor/accept/${doctorId}`).then(response =>  {
          if(response.ok) {
            table.draw()
              Swal.fire({
              title: "Doctor Approve!",
              text: "Succesfully approved!",
              icon: "success"
              });
          }
        })

      } else if (rejectBtn) {
             const doctorId  = rejectBtn.getAttribute("data-id")
                Swal.fire({
                title: "Are you sure you declined this doctor?",
                text: "The Doctor will be rejected!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, reject it!"
                }).then((result) => {
                 
              if (result.isConfirmed) {
                    Swal.fire({
                    title: "Note!",
                    text: "Hospital is rejected.",
                    icon: "success"
                    });
                  get(`/hospital/doctor/reject/${doctorId}`)
                  .then(response => {
                    if (response.ok) {
                        table.draw()
                    }
                  })

                }
                })
      }
    });

  function openViewDoctorModal(modal, image, address) {
        const doctorIdImage = modal._element.querySelector('.doctor-id-image')
        const doctorAddress = modal._element.querySelector('#doctor-address')

        doctorIdImage.src = "/img/doctor/" + image
        doctorAddress.innerHTML = address

    modal.show();
  }
}); 