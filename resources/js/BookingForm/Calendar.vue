<template>
    <div>
        <CalendarWeek v-if="isWeekView" @selectSlot="selectSlot" :options="options" :service="service" :initIntervalsCollection="initIntervalsCollection"
                    :timeprops="timeprops" :staffs="staffs" :duration="duration" :location="location" :viewData="viewData"  />
        <CalendarMonth v-else @selectSlot="selectSlot" :options="options" :service="service" :initIntervalsCollection="initIntervalsCollection"
                    :timeprops="timeprops" :staffs="staffs" :duration="duration" :location="location" :viewData="viewData"  />
    </div>
</template>

<script>

import CalendarMonth from './CalendarMonth'
import CalendarWeek from './CalendarWeek'

export default {
    props: ['options','service','initIntervalsCollection', 'timeprops', 'staffs', 'duration', 'location', 'viewData','rescheduling', 'relations'],
    components: {
        CalendarMonth, CalendarWeek
    }, 
    computed: {
        isWeekView(){
            return [undefined,false].indexOf(this.options.selection.check_viewweek) === -1
        }
    },
    methods: {
        selectSlot(slot){
            let next = this.relations.next
            this.$emit('selectSlot', this.rescheduling ? 'RescheduleConfirm':next, {selectedSlot: slot})
        },
    }

}
</script>


