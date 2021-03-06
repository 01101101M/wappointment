<template>
    <div class="wap-front" :class="getDynaClasses" :id="elementId">
        <StyleGenerator :options="opts" :wrapper="elementId" :largeVersion="largeVersion"></StyleGenerator>
        <div v-if="isPage" :class="'step-'+stepName">
            <BookingForm v-if="isBookingPage" :options="opts" :wrapperid="elementId" @changedStep="stepChanged"></BookingForm>
            <ViewingAppointment v-else  :options="opts" :view="getParameterByName('view')" :appointmentkey="getParameterByName('appointmentkey')"></ViewingAppointment>
        </div>
        
        <div class="wap-wid wclosable" :class="'step-'+stepName" v-if="isWidget">
            <span v-if="bookForm && isBottomRight" @click="backToButton" class="wclose"></span>
            <BookingForm v-if="bookForm" :step="currentStep" :options="opts" :wrapperid="elementId" :passedDataSent="dataSent" @changedStep="stepChanged"></BookingForm>
            <BookingButton v-else @click="toggleBookForm" class="wbtn wbtn-booking wbtn-primary" :options="opts" >{{ realButtonTitle }}</BookingButton>
        </div>
        <div class="wap-bg" v-if="bgEnabled" @click="backToButton"></div>
    </div>
</template>

<script>

import BookingButton from './BookingForm/BookingButton'
import StyleGenerator from './Components/StyleGenerator'
import Colors from './Modules/Colors'
import UrlParam from './Modules/UrlParam'
import ckl from './Standalone/chunkloader.js'

export default {
    mixins: [Colors, UrlParam],
    props: ['classEl', 'options', 'step', 'attributesEl'],
    components: { 
        BookingButton,
        StyleGenerator,
        ViewingAppointment: () => (ckl(import(/* webpackChunkName: "group-viewingappointment" */ './BookingForm/ViewingAppointment'))),
        BookingForm: () => (ckl(import(/* webpackChunkName: "group-bookingform" */ './BookingForm/Main')))
    }, 
    data: () => ({
        bookForm: false,
        currentStep: null,
        dataSent: null,
        opts: null,
        elementId: '',
        stepName: '',
        disabledButtons: false,
        buttonTitle: '',
        brFixed: undefined,
        largeVersion: false
    }),
    created(){
      this.elementId = 'wapfrontwrapper-' + Date.now()
      if(this.step !== undefined) this.currentStep = this.step
      this.opts = this.options === undefined ? window.widgetWappointment : Object.assign ({}, this.options)
      if(this.opts.demoData !== undefined){
          this.disabledButtons = true
      }

      this.processShortcode()
    },

     watch:{
        step(val){
            this.currentStep = val
        },
        currentStep(val){
            if(val !== undefined) {
              
                this.bookForm = (['button'].indexOf(val) === -1) ? true:false
                //this.bookForm = val > 1 ? true:false
            }
        },
        bookForm(val){
          if(val === false) this.$emit('changedStep','button')
          //else this.currentStep = 2
        }
    },
    computed:{
        bgEnabled(){
          return this.bookForm && this.isBottomRight
        },
        getDynaClasses(){
          let classes = {
            'br-fixed': this.isBottomRight, 
            'large-version': this.largeVersion,
            'wmobile': this.isMobilePhone,
          }
          
          classes[this.getParameterByName('view')] = true
          return classes
        },

        isMobilePhone(){
          return window.innerWidth < 500 && window.innerHeight > window.innerWidth
        },

        realButtonTitle(){
          return this.options!== undefined && this.options.button.title !== undefined ? this.options.button.title : this.buttonTitle
        },
        isPage(){
            return this.classEl === 'wappointment_page'
        },
        isBookingPage(){
            return this.isPage && this.getParameterByName('view') === 'new-event'
        },
        isWidget(){
            return this.classEl === 'wappointment_widget'
        },
        isBottomRight(){
          return this.brFixed !== undefined && this.brFixed === true
        }

    },
    methods: {
        stepChanged(stepName){
          this.stepName = stepName
        },
        processShortcode(){
          if(this.attributesEl !== undefined && Object.keys(this.attributesEl).length > 0){
            if(this.attributesEl.buttonTitle !== undefined) this.buttonTitle = this.attributesEl.buttonTitle
            if(this.attributesEl.brcFloats !== undefined) this.brFixed = true
            if([undefined,false].indexOf(this.attributesEl.largeVersion) === -1) this.largeVersion = true
            if([undefined,false].indexOf(this.attributesEl.autoOpen) === -1 ) this.bookForm = true
            if([undefined,false].indexOf(this.attributesEl.week) === -1) this.opts.selection.check_viewweek = true
            
            this.opts.attributesEl = this.attributesEl
          }
        },
        backToButton(){
          if(this.bgEnabled && this.isMobilePhone){
                document.body.classList.remove("wappo-popped")
          }
          this.bookForm = false
        },
        toggleBookForm() {
            this.bookForm = !this.bookForm
            if(this.bgEnabled && this.isMobilePhone){
                document.body.classList.add("wappo-popped")
            }
        },
    }
}
</script>
<style>
.large-version .wap-wid{
    max-width: 420px;
}
.large-version .wap-wid.step-BookingCalendar{
    max-width: 100%;
}
.wap-wid{
    max-width: 360px;
}

.br-fixed .wap-wid .wbtn-booking{
  float:right;
}
.wap-wid .loader {
    min-height: 68px;
}
.wap-front{
    font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,Noto Sans,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji;
}

.wap-front .wbtn.wbtn-secondary.wbtn-cell{
  border-radius: .5em;
  margin: .4em 0;
}

.wap-front .wbtn {
  display: inline-block;
  font-weight: 400;
  text-align: center;
  white-space: nowrap;
  vertical-align: middle;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
  border: 1px solid transparent;
  padding: 0.375em;
  font-size: .8em;
  line-height: 1.5;
  border-radius: 0.25em;
  transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
  margin: 0;
  background-color: transparent;
  background: transparent;
  height: auto;
  text-transform: none;
}


  .wap-front .wbtn:hover, 
  .wap-front .wbtn:focus {
    text-decoration: none;
  }

  .wap-front .wbtn:focus, 
  .wap-front .wbtn.focus {
    outline: 0;
  }

  .wap-front .wbtn.wbtn-disabled, 
  .wap-front .wbtn:disabled {
    opacity: 0.65;
  }

  .wap-front .wbtn:not(:disabled):not(.wbtn-disabled) {
    cursor: pointer;
  }

  .wap-front .wbtn:not(:disabled):not(.wbtn-disabled):active, 
  .wap-front .wbtn:not(:disabled):not(.wbtn-disabled).active {
    background-image: none;
  }

  .wap-front a.wbtn.wbtn-disabled,
  .wap-front fieldset:disabled a.wbtn {
    pointer-events: none;
  }

.wap-front .d-flex {
  display: -ms-flexbox !important;
  display: flex !important;
}
.wap-front .d-flex.d-flex-inline{
    display: inline-flex !important;
}
.wap-front .flex-wrap {
    -ms-flex-wrap: wrap !important;
    flex-wrap: wrap !important;
}

.wap-front .flex-fill{
    -ms-flex: 1 1 auto !important;
    flex: 1 1 auto !important;
}
.wap-front .justify-content-between {
  -ms-flex-pack: justify !important;
  justify-content: space-between !important;
}

.wap-front .justify-content-around {
  -ms-flex-pack: distribute !important;
  justify-content: space-around !important;
}
.wap-front .justify-content-center {
    -webkit-box-pack: center !important;
    -ms-flex-pack: center !important;
    justify-content: center !important;
}
.wap-front .align-items-center {
  -ms-flex-align: center !important;
  align-items: center !important;
}
.wap-front .align-content-around {
  -ms-flex-line-pack: distribute !important;
  align-content: space-around !important;
}
.wap-front .align-self-center {
  -ms-flex-item-align: center !important;
  align-self: center !important;
}

.wap-front .align-self-stretch {
  -ms-flex-item-align: stretch !important;
  align-self: stretch !important;
}

.wap-front.br-fixed{
    position: fixed;
    right: 0;
    bottom: 0;
    margin: 1rem;
    z-index: 9999999;
    max-height: 95%;
}

.wap-wid.wclosable > .wclose {
    position: absolute;
    z-index: 1;
    right: 2px;
    top: 2px;
}
.wap-wid.wclosable > .wclose::before, 
.wap-wid.wclosable > .wclose::after {
    background-color: #b5b1b1;
}
.wap-wid.wclosable > .wclose:hover::before, 
.wap-wid.wclosable > .wclose:hover::after {
    background-color: #575656;
}

.wap-front .wml-2{
    margin-left:.4em;
}
.wprice{
  font-weight: bold;
}

.wap-front .text-center{
    text-align: center;
}

.wap-front .text-sm{
  font-size: .7em;
}


.wappo-popped{
  overflow: hidden;
}

@media only screen and (max-width: 500px) {
  .wap-front.br-fixed .wap-bg{
    position: fixed;
    height: 100%;
    width: 100%;
    background-color: rgba(0, 0, 0, .7);
    top: 0;
    z-index: -1;
  }
  .wap-front.br-fixed{
    width: 100%;
    margin: 0;
  }

  .wap-front.br-fixed .wbtn.wbtn-booking.wbtn-primary{
    margin: 0;
    width:100%;
    border-radius: .2rem .2rem 0 0;
  }

  .wap-front.br-fixed .wap-bf,
  .wap-front.br-fixed .wap-wid{
    max-width: 100%;
  }
}
</style>
