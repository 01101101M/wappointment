<template>
  <div >
      <WPNotice>
          <p>There are pending database updates : <button class="btn btn-primary btn-sm" @click="runMigrate">Run updates</button></p>
          <p><small class="text-muted">It's always a good idea to create a database backup before running an update.</small></p>
      </WPNotice>
  </div>
</template>
<script>
import abstractView from '../Views/Abstract'
import AppService from '../Services/V1/App'
import WPNotice from '../WP/Notice'
import Helpers from '../Modules/Helpers'
export default {
    components: {WPNotice},
    extends: abstractView,
    mixins:[Helpers],
    data: () => ({
        serviceApp: null,
    }),
    created(){
        this.serviceApp = this.$vueService(new AppService)
    },
    methods: {
        runMigrate(){
            this.request(this.runMigrateRequest, {}, undefined, false, this.successUpdate)
        },
        async runMigrateRequest(){
          return await this.serviceApp.call('migrate') 
        },
        successUpdate(result){
            this.$WapModal().notifySuccess(result.data.message)
            this.$WapModal()
            .request(this.sleep(4000))
          window.location = window.apiWappointment.base_admin + '?page=wappointment_calendar'
        }

    }
}
</script>
