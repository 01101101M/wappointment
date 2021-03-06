<template>
    <div :class="classColumn">
        <strong class="columnTitle">{{ daykey }}</strong>
        <div :id="daykey" :class=" active ? 'box-shadow active' : 'box-shadow'" :style="'height:'+getHeightColumn()+'px'"
            draggable="true" 

            @mousedown.prevent.stop="dragging"
            @touchstart.prevent.stop="dragging"
            @mousemove="draggingMove"
            @dragstart.prevent.self="startCreate"
            @mouseup.prevent.stop="stopCreate"
            @touchend.prevent.self="stopCreate">
            
            <div class="drag-helper" :style="getWrapperGhostStyle"></div>
            <div class="ghost-wrapper" :style="getWrapperGhostStyle">
                <div v-if="isAtLeastOneSlot" :style="getGhostStyle" class="ghost">
                    <strong class="timeText">{{ ghost[0] }}h - {{ ghost[1] }}h</strong>
                </div>
            </div>
            <div class="events" v-if="!hiddenTimes">
                
                <timeBlock v-for="(timeBlock, tblockid) in openedTimes" :key="minHour+maxHour+daykey+tblockid+timeBlock[0]+timeBlock[1]" 
                :tblockid="tblockid" :timeBlock="timeBlock" 
                :daykey="daykey" :y="getY(timeBlock[0])" :h="getHeight(timeBlock)"
                @deletedBlock="deletedBlock" 
                @updatedBlock="updatedBlock"
                @active="activeDay"
                @deactive="deactiveDay"
                @editBlock="editBlock"
                ></timeBlock>
            
            </div>
        </div>
    </div>
</template>

<script>
import timeBlock from '../Components/TimeBlock'

let timeBlockComps = window.wappointmentExtends.filter('RegavTimeBlockComponent', {'timeBlock': timeBlock} )
export default {
    props: ['daykey', 'openedTimes', 'minHour', 'maxHour', 'classColumn', 'heightUnit'],
    data() {
        return {
            notchSize:1,
            startDragAt:0,
            currentDrag:0,
            isDragging:false,
            ghost: [],
            lastLayerY: 0,
            active: false,
            activeBlock: [],
            hiddenTimes: false
        }
    },
    components: timeBlockComps, 
    computed: {
        isAtLeastOneSlot(){
            return this.isDragging && (this.ghost[1]-this.ghost[0]) != 0
        },
        getGhostStyle(){
          if(this.ghost.length > 0) return 'height:'+this.getHeight(this.ghost)+'px;top:'+this.getY(this.ghost[0])+'px;'
          return ''
        },
        getWrapperGhostStyle(){
          if(this.ghost.length > 0) return 'position:absolute;height: 100%;width: 100%;'
          return ''
        }
    },
    mounted(){
        this.deactiveDay()
    },
    methods: {

      activeDay(){
          this.active = true
      },
      deactiveDay(){
          this.active = false
      },
      startCreate(e){
          this.initGhost(e)
      },

      initGhost(e){
          this.startDragAt = e.layerY
          this.currentDrag = this.startDragAt + this.heightUnit
      },

      stopCreate(e){
          if(this.isDragging) this.createBlock(e)
          
          this.ghost = []
          this.deactiveDay()
      },

      dragging(e){
          this.initGhost(e)
          this.isDragging = true
          this.activeDay()
      },

      draggingMove(e){
          
         //if(this.isDragging)console.log(e)
         if(!this.isDragging || !(e.target.className=='events' || e.target.className=='drag-helper' || e.target.className=='ghost')) return;
          if(e.target.className=='events' || e.target.className=='drag-helper' ) this.lastLayerY = e.layerY

          this.currentDrag = e.layerY


          if(e.target.className=='ghost') this.currentDrag += this.lastLayerY
          //if(e.target.className.substr(0, 3)=='vdr') this.currentDrag = this.lastLayerY - (e.target.clientHeight - e.layerY)
          if(this.startDragAt > this.currentDrag){
              
              this.ghost = [this.convertYToHour(this.currentDrag), this.convertYToHour(this.startDragAt)]
          }else{
              
              this.ghost = [this.convertYToHour(this.startDragAt), this.convertYToHour(this.currentDrag)]
          }

      },
      
      getHeight(timeBlock){
          //console.log('get height block',timeBlock[1],timeBlock[0],this.heightUnit)
          //console.log('extra',(timeBlock[1]-timeBlock[0]) * this.heightUnit ,this.y,this.getY(timeBlock[0]))
          return (timeBlock[1]-timeBlock[0]) * this.heightUnit 
      },

      getY(hour){
          return (hour - this.minHour) * this.heightUnit 
      },

      getHeightColumn(){
          return (this.maxHour - this.minHour) * this.heightUnit 
      },

      openingTimes(){
        let opening_times = []
        for (let index = this.minHour; index < this.maxHour; index++) {
          opening_times.push(index+'h') 
        }
        return opening_times
      },
        
      deletedBlock( tblockid){
          let openedTimes = this.openedTimes
          openedTimes.splice(tblockid, 1)

          this.$emit('updatedSlots', this.daykey, openedTimes)
      },

      createBlock(event){

            this.currentDrag = this.startDragAt = 0
            this.isDragging = false

            let openedTimes = this.openedTimes
            if(this.ghost.length == 0) this.ghost=[this.convertYToHour(event.layerY), this.convertYToHour(event.layerY+this.heightUnit)]

            openedTimes.push(this.ghost)
            this.$emit('updatedSlots', this.daykey, openedTimes)
            this.ghost = []
      },


      updatedBlock(tblockid, hstart, hend, original){
          let openedTimes = this.openedTimes
          original[0] = hstart
          original[1] = hend
          openedTimes[tblockid] = original
          this.hiddenTimes = true
          this.$emit('updatedSlots', this.daykey, openedTimes)
           this.$nextTick()
          this.hiddenTimes = false
          this.$nextTick() 
      },

      editBlock(tblockid, hstart, hend, tblock){
          this.$emit('editBlock', this.daykey, hstart, hend, tblock)
      },

      convertYToHour(val){
          return this.minHour + Math.floor(val/this.heightUnit)
      },

  } 
}
</script>
<style>
    .ghost{
        background-color: rgb(185, 206, 212);
        position: absolute;
        width: 100%;
        border-radius:.6rem;
    }
    .drag-helper{
        position: relative;
        z-index: 6;
    }
    .ghost-wrapper{
        position: relative;
        z-index: 5;
    }
    .events{
        position: absolute;
        z-index: 4;
        height: 100%;
        width: 100%;
    }
    .timeText{
        color:#fff;
        font-size: 1rem;
    }
    .columnTitle{
        font-size: .9rem;
        text-transform: capitalize;
        font-weight: normal;
        color:#acacac;
    }
    .day-column .columnTitle{
        font-weight: bold;
    }
    @media (max-width: 769px) { 
        .columnTitle{
            font-size: .8rem;
        }

        .columnTitle .dashicons, .columnTitle .dashicons-before::before {
            display: inline-block;
            width: 14px;
            height: 14px;
            font-size: 14px;
            vertical-align: sub;
        }
    }
</style>