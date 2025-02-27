import { SQLite, SQLiteObject } from '@ionic-native/sqlite/ngx';
import { File } from '@ionic-native/file/ngx';
import { NativeStorage } from "@ionic-native/native-storage/ngx";
import { Documentos } from '../models/documentos';

export class dataResetLocal {
    public sqlite: SQLite;
    public db = null;
    public file: File;
    private nativeStorage: NativeStorage;

    constructor() {
        this.sqlite = new SQLite();
        this.file = new File();
        this.nativeStorage = new NativeStorage();
    }

    public async data() {
        let basesdatas: any = await this.nativeStorage.getItem("DB");
        return new Promise((resolve, reject) => {
            this.sqlite
                .create({
                    name: basesdatas + "xm.db",
                    location: "default",
                })
                .then((db: SQLiteObject) => {
                    resolve(db);
                })
                .catch((e) => {
                    reject(e);
                });
        });
    }



    deleteNativeStorage = async () => {

        console.log("DEVD deleteNativeStorage ")
        return this.nativeStorage.clear()
    }

    deleteDatabase = async () => {
        // return new Promise(async (resolve, reject) => {
        let basesdatas: any = await this.nativeStorage.getItem("DB");
        console.log("DEVD database to delete ", basesdatas);
        this.sqlite
            .deleteDatabase({
                name: basesdatas + "xm.db",
                location: "default",
            })
            .then(() => {
                console.log("DEVD Success deleted database");
                // resolve('DEVD Success deleted')

                //    return this.sqlite.create({
                //      name: 'db.db',
                //      location: 'default'
                //    })
            })
            .catch((e) => console.log("DEVD error in delete database ", e));

    };

    async alterTables(documents: any[]) {
        console.log("alterar", documents);
        if(documents){
            const classDocuments = new Documentos();
            let tableMovil = "";
            if (documents.length > 0) {
                documents.forEach(async (item) => {
                    console.table(item);
                    let rowNames: any = await classDocuments.camposCabeceraDocumentos(item.table);
                    rowNames = rowNames.map((row) => {
                        return row.name;
                    })
                    console.log(" rowsNmaes ", rowNames);
                    console.log("BUSCAR ", String(item.nameCampo));

                    const rta = rowNames.some((element) => element == item.nameCampo);
                    console.log({ rta });
                    if (!rta) {
                        try {
                            await classDocuments.alterTableRun(item.table, item.nameCampo, item.typeCampo);
                            console.table({ "STATUS ALTER ": "SUCCESS", "TABLE": item.table });
                            console.log(" SELECT * TABLE  ", await classDocuments.selectDinamic(item.table));


                        } catch (error) {
                            console.error(error);

                        }
                        console.log("classDocuments clientes", await classDocuments.selectClients());
                    } else {
                        console.log("YA EXISTE EL CAMPO " + item.nameCampo + " en " + item.table);
                        console.log(" SELECT * TABLE  ", await classDocuments.selectDinamic(item.table));

                    }
                })
            }
        }

    }

    deleteLocalStorage = async () => {
        // localStorage.removeItem('');
        console.log("DEVD do something")


    }

}

