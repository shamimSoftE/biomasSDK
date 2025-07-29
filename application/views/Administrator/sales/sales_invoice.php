<style>
  .v-select {
    float: right;
    min-width: 100%;
    background: #fff;
    margin-left: 5px;
    border-radius: 4px !important;
    margin-top: -2px;
  }

  .v-select .dropdown-toggle {
    padding: 0px;
    height: 25px;
    border: none;
  }

  .v-select input[type=search],
  .v-select input[type=search]:focus {
    margin: 0px;
  }

  .v-select .vs__selected-options {
    overflow: hidden;
    flex-wrap: nowrap;
  }

  .v-select .selected-tag {
    margin: 2px 0px;
    white-space: nowrap;
    position: absolute;
    left: 0px;
  }

  .v-select .vs__actions {
    margin-top: -5px;
  }

  .v-select .dropdown-toggle .clear {
    margin-top: 5px;
  }

  .v-select .dropdown-menu {
    width: auto;
    overflow-y: auto;
  }
</style>

<div id="salesInvoiceReport" class="row" style="margin: 0;">
  <fieldset class="scheduler-border scheduler-search">
    <legend class="scheduler-border">Search Sales Invoice</legend>
    <div class="control-group">
      <div class="col-xs-12 col-md-12 col-lg-12">
        <div class="form-group" style="display: flex;align-items:center;">
          <label class="col-md-2 text-right col-md-offset-2 control-label no-padding-right"> Invoice no: </label>
          <div class="col-md-4">
            <v-select v-bind:options="invoices" label="invoice_text" v-model="selectedInvoice" v-on:input="viewInvoice" @search="onSearchInvoice" placeholder="Select Invoice"></v-select>
          </div>
        </div>

        <div class="form-group">
          <div class="col-md-2">
            <input type="button" class="btn btn-primary" value="Show Report" v-on:click="viewInvoice" style="margin-top:0px;width:150px;display: none;">
          </div>
        </div>
      </div>
    </div>
  </fieldset>

  <div class="col-md-8 col-md-offset-2">
    <br>
    <sales-invoice v-if="company_profile.print_type != 3 && showInvoice" v-bind:sales_id="selectedInvoice ? selectedInvoice.SaleMaster_SlNo : ''"></sales-invoice>
    <pos-invoice v-if="company_profile.print_type == 3 && showInvoice" v-bind:pos_id="selectedInvoice ? selectedInvoice.SaleMaster_SlNo : ''"></pos-invoice>
  </div>
</div>



<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/components/salesInvoice.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/components/posInvoice.js"></script>

<script>
  Vue.component('v-select', VueSelect.VueSelect);
  new Vue({
    el: '#salesInvoiceReport',
    data() {
      return {
        invoices: [],
        selectedInvoice: null,
        showInvoice: false,
        company_profile: {},
      }
    },
    created() {
      this.getSales();
      this.getCompanyProfile();
    },
    methods: {
      getCompanyProfile() {
        axios.get('/get_company_profile').then(res => {
          this.company_profile = res.data;
        })
      },
      getSales() {
        axios.post("/get_sales", {
          forSearch: 'yes'
        }).then(res => {
          this.invoices = res.data.sales;
        })
      },
      async onSearchInvoice(val, loading) {
        if (val.length > 3) {
          loading(true);
          await axios.post("/get_sales", {
              name: val,
            })
            .then(res => {
              let r = res.data;
              this.invoices = r.sales
              loading(false)
            })
        } else {
          loading(false)
          await this.getSales();
        }
      },
      async viewInvoice() {
        this.showInvoice = false;
        await new Promise(r => setTimeout(r, 500));
        this.showInvoice = true;
      }
    }
  })
</script>