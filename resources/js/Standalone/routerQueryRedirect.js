import parseQuery from './parseQuery'
export default function(router){
  let result = parseQuery(window.location.search.replace('?',''))
  if(result.page !== undefined && result.page.startsWith('wappointment_')) {
      if(result.page == 'wappointment_settings') {
          if(result.page == 'wappointment_settings' && window.location.hash.indexOf('#/') !== -1){
              router.push({ name: window.location.hash.replace('#/','')})
          }else{
              router.push({ name: 'wappointment_settings'})
          }
      }else{
          router.push({ name: result.page})
      }
      
  }else{
      router.push({ name: 'wappointment_calendar'})
  }
}

