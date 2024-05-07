import { Modal } from "bootstrap";
import { get, post, del } from "../ajax";
import DataTable from "datatables.net";
import Swal from 'sweetalert2';

window.addEventListener("DOMContentLoaded", function () {


  const table = new DataTable("#patientHospitalReferralTable", {
    serverSide: true,
    ajax: "/hospital/refer/data/load",
    orderMulti: false,
    columns: [

      { data: "patient" },
      { data: "hospital" },
      { data: "referralCode" },
      { data: "createdAt" },
      { data: "status" },
      {
        sortable: false,
        data: (row) => `
                    <div class="d-flex">
                        <button class="ms-2 btn btn-outline-primary print-refer-btn" data-id="${row.id}" >
                           Print Referral
                        </button>
                        <button class="ms-2 btn btn-outline-danger cancel-refer-btn" data-id="${row.id}" >
                           Cancel
                        </button>
                        
                        
                    </div>
                `,
      },
    ],
  });

  document.querySelector("#patientHospitalReferralTable").addEventListener("click", function (event) {
    const cancelBtn = event.target.closest(".cancel-refer-btn");
    const printRefer = event.target.closest(".print-refer-btn");

   if (cancelBtn) {
        const referId = cancelBtn.getAttribute("data-id");
        Swal.fire({
          title: "Are you sure?",
          text: "The Request will be cancel!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, cancel it!",
        }).then((result) => {
          if (result.isConfirmed) {
            Swal.fire({
              title: "Cancelled!",
              text: "Request has been cancelled",
              icon: "success",
            });
            del(`/hospital/refer/reject/${referId}`).then((response) => {
              if (response.ok) {
                table.draw();
              }
            });
          }
        });
       } else if (printRefer) {
        const referId = printRefer.getAttribute("data-id");
         window.open(`/hospital/refer/${referId}/referral`, `_blank`);
       }
    })
})