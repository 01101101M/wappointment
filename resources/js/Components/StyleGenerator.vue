<template>
    <v-style>
        :root {
            --wappo-pri-bg: {{ hx_rgb(opts.colors.primary.bg) }};
            --wappo-pri-tx: {{ hx_rgb(opts.colors.primary.text) }};
            --wappo-sec-bg: {{ hx_rgb(opts.colors.secondary.bg) }};
            --wappo-sec-tx: {{ hx_rgb(opts.colors.secondary.text) }};
            --wappo-sec-sel-bg: {{ hx_rgb(opts.colors.secondary.bg_selected) }};
            --wappo-sec-sel-tx: {{ hx_rgb(opts.colors.secondary.text_selected) }};
        }
        .wap-front .wbtn.wbtn-primary-light,
        .wap-front .wbtn.wbtn-primary-light:not(:disabled):not(.disabled):active, 
        .wap-front .wbtn.wbtn-primary-light:not(:disabled):not(.disabled).active {
            background: {{ hx_rgb(opts.colors.primary.bg, .8) }};
            background-color: {{ hx_rgb(opts.colors.primary.bg, .8) }};
            border-color: {{ hx_rgb(opts.colors.primary.bg, .6) }};
            color: {{ hx_rgb(opts.colors.primary.text) }};
            box-shadow: 0 .1em 0 {{ hx_rgb(opts.colors.primary.bg, .4) }};
        }
        .wap-front .wbtn.wbtn-primary.wbtn-booking,
        .wap-front .wbtn-primary.wbtn-booking:not(:disabled):not(.disabled):active, 
        .wap-front .wbtn-primary.wbtn-booking:not(:disabled):not(.disabled).active {
        font-size: {{ opts.button.slide_size+'em' }} !important;
        {{ buttonIsBold }}
        {{ buttonIsFull }}
        }
        .wap-front .wbtn.wbtn-primary,
        .wap-front .wbtn-primary:not(:disabled):not(.disabled):active, 
        .wap-front .wbtn-primary:not(:disabled):not(.disabled).active {
            background: {{ hx_rgb(opts.colors.primary.bg) }};
            background-color: {{ hx_rgb(opts.colors.primary.bg) }};
            border-color: {{ hx_rgb(opts.colors.primary.bg, .8) }};
            color: {{ hx_rgb(opts.colors.primary.text) }};
            box-shadow: 0 .1em 0 {{ hx_rgb(opts.colors.primary.bg, .6) }};
        }
        .wap-front .wbtn.wbtn-primary:hover {
        background-color: {{ hx_rgb(opts.colors.primary.bg,.9) }};
        box-shadow: 0 0 0 .2em {{ hx_rgb(opts.colors.primary.bg,.5) }};
        }

                .wap-front .wbtn:focus, 
        .wap-front .wbtn.focus {
        box-shadow: 0 0 0 .2em {{ hx_rgb(opts.colors.primary.bg, .25) }};
        }
        
        .wap-front .wbtn-primary.disabled, 
        .wap-front .wbtn-primary:disabled, 
        .wap-front .wbtn-primary.disabled:hover, 
        .wap-front .wbtn-primary:disabled:hover {
            background-color: {{ hx_rgb(opts.colors.primary.bg) }} !important;
            border-color: {{ hx_rgb(opts.colors.primary.bg, .8) }} !important;
            color: {{ hx_rgb(opts.colors.primary.text) }} !important;
            box-shadow: 0 .1em 0 {{ hx_rgb(opts.colors.primary.bg, .6) }};
        }

        .wap-front .wbtn-primary:disabled:hover {
            background-color: {{ hx_rgb(opts.colors.primary.bg) }} !important;
            border-color: {{ hx_rgb(opts.colors.primary.bg, .8) }} !important;
            color: {{ hx_rgb(opts.colors.primary.text) }} !important;
            box-shadow: 0 .1em 0 {{ hx_rgb(opts.colors.primary.bg, .6) }};
        }

        .wap-front .address-service {
            background-color: {{ hx_rgb(opts.colors.address.bg) }};
            border-color: {{ hx_rgb(opts.colors.address.bg, .8) }};
            color: {{ hx_rgb(opts.colors.address.text) }};
            box-shadow: 0 .1em 0 {{ hx_rgb(opts.colors.address.bg, .6) }};
        }
        .wap-front .address-service a{
            color: {{ hx_rgb(opts.colors.address.text) }};
        }
        .wap-front .wbtn.wbtn-secondary,
        .wap-front .wbtn-secondary:not(:disabled):not(.disabled):active, 
        .wap-front .wbtn-secondary:not(:disabled):not(.disabled).active {
            background: {{ hx_rgb(opts.colors.secondary.bg) }};
        background-color: {{ hx_rgb(opts.colors.secondary.bg) }};
        border-color: {{ hx_rgb(opts.colors.secondary.bg, .8) }};
        color: {{ hx_rgb(opts.colors.secondary.text) }};
        box-shadow: 0 .1em 0 {{ hx_rgb(opts.colors.secondary.bg, .6) }};
        margin: .4em 0;
        }
        .wap-front .wbtn.wbtn-secondary:hover {
        background-color: {{ hx_rgb(opts.colors.secondary.bg,.9) }};
        box-shadow: 0 0 0 .2em {{ hx_rgb(opts.colors.secondary.bg,.5) }};
        }

        .wap-front .wbtn.wbtn-secondary.selected{
        background-color: {{ hx_rgb(opts.colors.secondary.bg_selected) }};
        border-color: {{ hx_rgb(opts.colors.secondary.bg_selected, .8) }};
        color: {{ hx_rgb(opts.colors.secondary.text_selected) }};
        box-shadow: 0 .1em 0 {{ hx_rgb(opts.colors.secondary.bg_selected, .6) }};
        }


        
        .wap-front .wap-head .staff-desc {
            color: {{ hx_rgb(opts.colors.header.text) }};
        }

        .wap-front .wap-form-body .timezone {
            background-color: {{ hx_rgb(opts.colors.body.bg, .9) }};
        }
        
        .wap-front .wap-form-body{
            background-color: {{ hx_rgb(opts.colors.body.bg) }};
            color: {{ hx_rgb(opts.colors.body.text) }};
        }

        .wap-front .wap-form-body .no-avail, 
        .wap-front .wap-form-body .wbtn-link.disabled{
            color: {{ hx_rgb(opts.colors.body.disabled_links) }};
        }

        .wap-front .wap-head {
            background-color: {{ hx_rgb(opts.colors.header.bg) }};
        }

        .wap-bf.show{
            border-radius: {{ calendarRound }}em;
        }
        
        .wap-front .wbtn.wbtn-secondary.dayselected{
            background-color: {{ hx_rgb(opts.colors.selected_day.bg) }};
        }
        .wap-front .wap-form-body .dayselected span.avail{
            color: {{ hx_rgb(opts.colors.selected_day.text) }};
        }

        .wap-front .wsummary-section {
            border-bottom: 1px solid {{ hx_rgb(opts.colors.body.text, .1) }};
        }
        .wap-front .wlabel{
            color: {{ hx_rgb(opts.colors.body.text) }};
        }

        .confirmation-summary span{
            color: {{ hx_rgb(opts.colors.body.text,.8) }};
        }
        .wtitle{
            color: {{ hx_rgb(opts.colors.body.text,.8) }};
            font-size: 1em;
            text-align:center;
        }
        .wdescription{
            color: {{ hx_rgb(opts.colors.body.text,.8) }};
        }
        
        .wap-front .wap-head img{
            border-radius: 50%;
        }

        .wap-front .wbtn-cell{
            text-align: center;
            padding: .4em;
            border-radius: {{ calendarRound }}em;
        }

        .wap-front .form-control:focus {
            color: #495057;
            background-color: #fff;
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 1px {{ hx_rgb(opts.colors.primary.bg, .25) }};
        }
        .wap-front .wselected{
            background-color: {{ hx_rgb(opts.colors.primary.bg, .5) }};
            color: {{ hx_rgb(opts.colors.primary.text) }};
        }

        .wap-front [data-tt]:before {
            background-color: {{ hx_rgb(opts.colors.primary.bg, .8) }};
            color: {{ hx_rgb(opts.colors.primary.text) }};
        }

        .wap-front [data-tt]:after {
            border-top: 5px solid {{ hx_rgb(opts.colors.primary.bg, .9) }};
        }

        .wap-front .phone-field:focus-within {
            box-shadow: 0 0 2px  {{ hx_rgb(opts.colors.primary.bg, .25) }};
            border-color: {{ hx_rgb(opts.colors.primary.bg) }};
        }
        .wap-front .field-required input:focus, 
        .wap-front .field-required select:focus {
            border-color: {{ hx_rgb(opts.colors.primary.bg) }};
            box-shadow: 0 0 2px {{ hx_rgb(opts.colors.primary.bg, .6) }};
            outline: 2px solid transparent;
        }
        .wap-front .confirmation-cell .success {
            background-color: {{ hx_rgb(opts.colors.confirmation.bg) }};
            color: {{ hx_rgb(opts.colors.confirmation.text) }};
            padding: .2em .8em;
            border-radius: 5px;
            overflow: hidden;
            font-size: .8em;
        }
        .wap-booking-fields .wap-field{
            margin-bottom:.4em
        }
        .wap-booking-fields .isInvalid input[type="text"], 
        .wap-booking-fields .isInvalid input[type="email"], 
        .wap-booking-fields .isInvalid input[type="url"], 
        .wap-booking-fields .isInvalid input[type="tel"],
        .wap-booking-fields .isInvalid textarea{
            border-right: 4px solid {{ hx_rgb(opts.colors.form.error) }} !important;
        }
        .wap-booking-fields .isValid input[type="text"], 
        .wap-booking-fields .isValid input[type="email"], 
        .wap-booking-fields .isValid input[type="url"], 
        .wap-booking-fields .isValid input[type="tel"],
        .wap-booking-fields .isValid textarea{
            border-right: 4px solid {{ hx_rgb(opts.colors.form.success) }} !important;
        }
        .wap-booking-fields .field-required label::after {
            content:" *";
            color:{{ hx_rgb(opts.colors.form.error) }};
        }

        .wap-front .wappointment-errors{
            background-color:{{ hx_rgb(opts.colors.form.error) }};
            color:#fff;
            border-radius:.25em;
            padding: .3em;
            margin: .5em 0;
        }
        .wap-front .text-sm{
            color: {{ hx_rgb(opts.colors.body.tx_small) }};
        }

        /** wrapper tag **/
        {{ '#'+wrapper }}.wap-front {
            font-size: {{ baseFontSize }};
        }
        
    </v-style>
</template>

<script>
import Colors from '../Modules/Colors'
export default {
    mixins: [Colors],
    props: ['options', 'wrapper', 'largeVersion'],
    data:() => ({
        opts: null,
        widthWrapper: false,
        baseFontSize: '18px',
        calendarRound: .6,
    }),
    created(){
        this.opts = Object.assign ({}, this.options)

    },
    computed:{
        buttonIsBold(){
            return this.opts.button.check_bold === true ? 'font-weight:bold;':''
        },
        buttonIsFull(){
            return this.opts.button.check_full === true ? 'width:100%;':''
        },
    },
    mounted(){
        this.widthWrapper = document.getElementById(this.wrapper).clientWidth
        if(this.largeVersion){
            if(this.widthWrapper > '620'){
                this.baseFontSize = '22px'
            }
            if(this.widthWrapper < '620'){
                this.baseFontSize = '21px'
            }
            if(this.widthWrapper < '520'){
                this.baseFontSize = '20px'
            }
            if(this.widthWrapper < '420'){
                this.baseFontSize = '19px'
            }
        }
        
        if(this.widthWrapper < '320'){
            this.baseFontSize = '18px'
        }
/*         if(this.widthWrapper < '230'){
            this.baseFontSize = '17px'
        }
        if(this.widthWrapper < '210'){
            this.baseFontSize = '16px'
        }
        if(this.widthWrapper < '190'){
            this.baseFontSize = '15px'
        }
        if(this.widthWrapper < '170'){
            this.baseFontSize = '14px'
        } */
    }
}
</script>
