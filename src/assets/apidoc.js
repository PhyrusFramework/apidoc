new VueController({

    data() {
        return {
            tree: null,
            launcher: null,
            headers: [{name: '', value: '', initialized: false}]
        }
    },

    created() {
        Ajax.get('getApidocData')
        .then(data => {
            this.tree = data;
        });
    },

    methods: {
        scrollTo(id) {
            elem('#apidoc-content').scrollToChild('#'+id);
        },

        classForMethod(method) {
            let cl = {}
            cl[method] = true;
            return cl;
        },

        launch(method, methodName) {
            this.launcher = {
                method: methodName,
                path: URL.host() + method.path,
                parameters: [{name: '', value: '', initialized: false}],
                response: null
            }
        },

        addParam(param) {
            if (param.initialized) return;
            param.initialized = true;

            if (param.name != '' || param.value != '') {
                this.launcher.parameters.push({name: '', value: '', initialized: false});
            }
        },

        addHeader(param) {
            if (param.initialized) return;
            param.initialized = true;

            if (param.name != '' || param.value != '') {
                this.headers.push({name: '', value: '', initialized: false});
            }
        },

        runRequest() {

            let data = {}
            for(let param of this.launcher.parameters) {
                if (param.name != '') {
                    data[param.name] = param.value;
                }
            }

            let headers = {}
            for(let header of this.headers) {
                if (header.name != '') {
                    headers[header.name] = header.value;
                }
            }

            http[this.launcher.method.toLowerCase()](this.launcher.path, {
                data: data,
                headers: headers
            })
            .then(response => {
                let res = response;
                if (typeof res != 'string')
                    res = JSON.stringify(res, null, 2);
                this.launcher.response = res;
            })
            .catch(err => {
                console.log("err", err);
            });
        },

        whereDesc(method, parameter) {
            
            let where = ['GET', 'DELETE'].includes(method.method) ? 'query' : 'data';

            if (parameter.where && ['query', 'data', 'url'].includes(parameter.where)) {
                where = parameter.where;
            }

            if (where == 'query') {
                return '<b>Query</b>: ' + method.path + '/?' + parameter.name + '=xxx';
            }
            if (where == 'url') {
                return '<b>URL</b>: url/:' + parameter.name;
            }
            return '<b>DATA</b>: { ' + parameter.name + ": 'xxx' }";

        },

        responseColor(c) {
            let code = parseInt(c);
            if (code >= 200 && code < 300) {
                return 'rgb(102, 212, 80)';
            }
            if (code >= 400) {
                return 'red';
            }
            return 'orange';
        }
    }

});