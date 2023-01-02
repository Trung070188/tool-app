const {  pad, run, cleanUp } = require('./lib');

async function buildBase() {

    const date = new Date;
    const version = date.getFullYear() + '' + pad(date.getMonth() + 1) + '' + pad(date.getDate());
    const tag = 'v' + version;
    const localTag = 'vnpd/php-fpm-8.1-nginx'
    const repo = 'containerregistry.vietnampost.vn/ewallet-vnpd/php-fpm-8.1-nginx';

    await run(`docker build -t ${localTag}:${tag} -f Dockerfile.base .`);
    await run(`docker tag ${localTag}:${tag} ${repo}:${tag}`);
    await run(`docker push ${repo}:${tag}`)
}

async function main() {

    const date = new Date;
    const version = date.getFullYear() + '' + pad(date.getMonth() + 1) + '' + pad(date.getDate());
    //const tag = 'v' + version;
    const tag = 'latest';
    const localTag = 'vnpd/sbv-report-cms'
    const repo = 'containerregistry.vietnampost.vn/ewallet-vnpd/sbv-report-cms';
    //await run('cd src && yarn build')
    await run('rm -rf src/public/files/*');

    await run(`docker build  --network=host -t ${localTag}:${tag} .`);
    await run(`docker tag ${localTag}:${tag} ${repo}:${tag}`);
    await run(`docker tag ${localTag}:${tag} ${repo}:latest`);
    await run(`docker push ${repo}:latest`)

    //await cleanUp('ewallet-web-manager');
}

if (process.argv[2] === 'base') {

    buildBase().catch(err => {
        console.error(err)
    })

} else {
    main().catch(err => {
        console.error(err)
    })
}
