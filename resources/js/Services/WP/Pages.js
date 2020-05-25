import ApiV2 from './ApiV2'
export default class PagesService extends ApiV2{

    endpoints() {
        return {
            create: { 
                method: 'post',
                route: '/pages',
            },
        };
    }

}