export class Tiempo {
    public static formatFecha(fecha: any): string {
        let today: any = new Date(fecha);
        let dd: any = today.getUTCDate();
        let mm: any = today.getMonth() + 1;
        let yyyy: any = today.getFullYear();
        if (dd < 10)
            dd = `0${dd}`;
        if (mm < 10)
            mm = `0${mm}`;
        return `${yyyy}-${mm}-${dd.toString()}`;
    }

    public static fecha(): string {
        let today: any = new Date();
        let dd: any = today.getUTCDate();
        let mm: any = today.getMonth() + 1;
        let yyyy: any = today.getFullYear();
        if (dd < 10)
            dd = `0${dd}`;
        if (mm < 10)
            mm = `0${mm}`;
        return `${yyyy}-${mm}-${dd}`;
    }

    public static hora(): string {
        let hoy: any = new Date();
        return hoy.getHours() + ':' + hoy.getMinutes() + ':' + hoy.getSeconds();
    }
}
