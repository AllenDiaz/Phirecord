import { Modal } from "bootstrap";
import { get, post, del } from "../ajax";
import DataTable from "datatables.net";
import Swal from 'sweetalert2';

window.addEventListener("DOMContentLoaded", function () {

  const table = new DataTable("#patientReferralTable", {
    serverSide: true,
    ajax: "/hospital/refer/load",
    orderMulti: false,
    columns: [

      { data: "patient" },
      { data: "hospital" },
      { data: "referralCode" },
      { data: "createdAt" },
      {
        sortable: false,
        data: (row) => `
                    <div class="d-flex ">
                        <button class="ms-2 btn btn-outline-primary accept-refer-btn" data-id="${row.id}" >
                           Accept
                        </button>
                        <button class="ms-2 btn btn-outline-danger reject-refer-btn" data-id="${row.id}" >
                           Reject
                        </button>
                    </div>
                `,
      },
    ],
  });

  document.querySelector("#patientReferralTable").addEventListener("click", function (event) {
       const rejectBtn = event.target.closest(".reject-refer-btn");
       const acceptBtn = event.target.closest(".accept-refer-btn");

       if (rejectBtn) {
        const referId = rejectBtn.getAttribute("data-id");
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
            get(`/hospital/refer/reject/${referId}`).then((response) => {
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
       }
    })

});
