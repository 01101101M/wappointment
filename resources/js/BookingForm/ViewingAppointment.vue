<template>
    <div>
        <template v-if="view.indexOf('addonView') === 0">
          <component :is="view" :options="options"></component>
        </template>
        <template v-else>
            <div v-if="loadedAppointment">
                <div class="summary-event" :class="view">
                    <h2 v-if="!isSaveEventPage">{{getText('title')}}</h2>
                    <div>{{ client.name }} - {{ client.email }}</div>
                    <div><strong class="date-start">{{ startDatei18n }}</strong></div>
                    <div><strong>{{ service.name }}</strong> <DurationCell :show="true" :duration="service.duration"/></div>
                </div>
                <div v-if="isSaveEventPage">
                    <div>
                        <p>{{options.confirmation.savetocal}}</p>
                        <SaveButtons :selectedSlot="selectedSlot" :service="service" :appointment="appointment"
                        :staff="staff" :currentTz="currentTz" :physicalSelected="physicalSelected"></SaveButtons>
                    </div>
                </div>
                <div v-else>
                    <RescheduleForm v-if="showReschedule" :appointmentkey="appointmentkey" :rescheduleData="rescheduleData" :options="options" ></RescheduleForm>
                    <div v-if="showCancelConfirmation">
                        <div v-if="appointmentCanceled">
                            <p class="h4">{{getText('confirmed')}}</p>
                        </div>
                        <div v-else>
                            <div v-if="loading">
                                <WLoader></WLoader>
                            </div>
                            <div v-else>
                                <p class="h4">{{getText('confirmation')}}</p>
                                <button class="wbtn wbtn-primary" @click="cancelAppointmentConfirmed">{{getText('confirm')}}</button>
                            </div>
                        </div>
                        
                    </div>
                    <div v-if="!buttonClicked">
                        <template v-if="isReschedulePage">
                            <button v-if="canStillReschedule" class="wbtn wbtn-primary" :class="'wbtn-'+view" @click="rescheduleEvent">{{getText('button')}}</button>
                            <p class="h4" v-else>{{getText('toolate')}}</p>
                        </template>
                        
                        <template v-if="isCancelPage">
                            <button v-if="canStillCancel" class="wbtn wbtn-primary" :class="'wbtn-'+view" @click="cancelAppointment">{{getText('button')}}</button>
                            <p class="h4" v-else>{{getText('toolate')}}</p>
                        </template>
                        
                    </div>
                </div>
            </div>
            <div v-else>
                <div v-if="loading">
                    <WLoader></WLoader>
                </div>
                <div v-else>{{errorLoading}}</div>
            </div>
        </template>
        
    </div>
</template>

<script>

import Dates from '../Modules/Dates'
import AbstractFront from './AbstractFront'
import SaveButtons from './SaveButtons'
import Iframe from '../Components/Iframe'
import AppointmentService from '../Services/V1/Appointment'
import DurationCell from './DurationCell'
import RescheduleForm from './RescheduleForm'
import ViewingAppointmentMixin from './ViewingAppointmentMixin'

let mixins = {ViewingAppointmentMixin:ViewingAppointmentMixin}
mixins = window.wappointmentExtends.filter('ViewingAppointmentMixin', mixins)


let compos = { 
    SaveButtons,
    Iframe,
    RescheduleForm,
    DurationCell,
  }
  compos = window.wappointmentExtends.filter('FrontMainViews', compos )
  console.log('compos',compos)

export default {
     
    mixins: [Dates, mixins.ViewingAppointmentMixin],
    extends: AbstractFront,
    components: compos, 
    props: ['appointmentkey', 'view', 'options'],
    data: () => ({
        viewName: 'appointment',
        appointment: null,
        client: null,
        service: null,
        staff: null,
        serviceAppointment: null,
        loadedAppointment: false,
        loading: false,
        currentTz: 'UTC',
        date_format: '',
        date_time_union: '',
        time_format: '',
        buttonClicked: false,
        showReschedule: false,
        showCancelConfirmation: false,
        appointmentCanceled: false,
        errorLoading: '',
        disabledButtons: false,
        rescheduleData: null
        
    }),
    created(){
        this.currentTz = this.tzGuess()
        this.serviceAppointment = this.$vueService(new AppointmentService)
        this.viewData = this.view
        if(this.options.demoData !== undefined){
            let data = {
                data: this.options.demoData.appointmentData
            }
            this.appointmentLoaded(data)
            this.viewData = this.options.demoData.view
            this.disabledButtons = true
        }
    },
    mounted () {
        if(this.isReschedulePage || this.isCancelPage || this.isSaveEventPage) {
            if(this.options.demoData === undefined)this.loadAppointment()
        }
        
    },
    methods: {
        getText(textKey){
            return this.isCancelPage? this.options.cancel[textKey]:this.options.reschedule[textKey]
        },
        

        cancelAppointmentConfirmed(){
            if(this.disabledButtons) return false
            this.loading = true
            this.buttonClicked = true
            this.cancelAppointmentRequest()
            .then(this.appointmentCanceledMet)
            .catch(this.appointmentCancelError)
        },
        async cancelAppointmentRequest() {
            return await this.serviceAppointment.call('cancel', {
                appointmentkey: this.appointmentkey,
            })
        }, 

        appointmentCanceledMet(data){
            this.loading = false
            this.appointmentCanceled = true
        },
        appointmentCancelError(){
            //console.log('couldnt cancel')
        },
        rescheduleEvent(){
            if(this.disabledButtons) return false
            this.buttonClicked = true
            this.showReschedule = true
        },
        cancelAppointment(){
            this.buttonClicked = true
            this.showCancelConfirmation = true
        },

        
    },
    computed: {
        startDatei18n(){
            return this.appointment.converted !== undefined ? this.appointment.converted :this.getMoment(this.selectedSlot, this.currentTz).format(this.fullDateFormat)
        },
        canStillReschedule(){
            return this.getUnixNow() < this.appointment.canRescheduleUntil
        },
        canStillCancel(){
            return this.getUnixNow() < this.appointment.canCancelUntil
        },
        isReschedulePage(){
            return this.viewData == 'reschedule-event'
        },
        isCancelPage(){
            return this.viewData == 'cancel-event'
        },
        isSaveEventPage(){
            return this.viewData == 'add-event-to-calendar'
        },
        fullDateFormat(){
           return this.date_format + '[' + this.date_time_union + ']' + this.time_format
        },
        getEncodedAdress(){
            return encodeURIComponent(this.service.address)
        },
        selectedSlot(){
            return this.appointment.start_at
        },
        getIframeMap(){
            return 'https://maps.google.com/maps?width=100%&height=200&hl=en&q=' + this.getEncodedAdress + '&ie=UTF8&t=&z=14&iwloc=B&output=embed'
        },
        getMapAdress(){
            return 'https://www.google.com/maps/search/?api=1&query=' + this.getEncodedAdress
        },
        phoneSelected(){
            return this.appointment !== null && this.appointment.type == 'phone'
        },
        physicalSelected(){
            return this.appointment !== null && this.appointment.type == 'physical'
        },
        skypeSelected(){
            return this.appointment !== null && this.appointment.type == 'skype'
        },
        clientPhone(){
            return this.client.options.phone
        },
        clientSkype(){
            return this.client.options.skype
        },
    }
}
</script>
<style >
.summary-event{
    margin: 1rem 0;
}
</style>