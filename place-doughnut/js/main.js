import {ARButton} from "https://unpkg.com/three@0.126.0/examples/jsm/webxr/ARButton.js"
import {FBXLoader} from "https://unpkg.com/three@0.126.0/examples/jsm/loaders/FBXLoader.js"

let camera, scene, renderer
let controller, pointer
let hitTestSourceAvailable = false
let hitTestSource = null
let localSpace = null
let preloaded

init()
animate()

function init() {
    const container = document.createElement("div")
    document.body.appendChild(container)

    // SCENE
    scene = new THREE.Scene()
    scene.name = "myScene"

    // CAMERA
    camera = new THREE.PerspectiveCamera(
        60,
        window.innerWidth / window.innerHeight,
        0.1,
        100
    )
    scene.add(camera)
 
    // RENDERER
    renderer = new THREE.WebGLRenderer({
        antialias: true,
        alpha: true
    })

    renderer.setPixelRatio(window.devicePixelRatio)
    renderer.setSize(window.innerWidth, window.innerHeight) // setting renderer to window size
    renderer.xr.enabled = true // enabling XR for AR usage
    container.appendChild(renderer.domElement) // add renderer to div as domElement

    // CONTROLLER
    controller = renderer.xr.getController(0)
    controller.addEventListener("select", onSelect, false)
    scene.add(controller)

    // GEOMETRY 
    const ringGeometry = new THREE.RingBufferGeometry(0.15, 0.25, 16).rotateX(-Math.PI / 2)
    const ringMaterial = new THREE.MeshBasicMaterial()
    pointer = new THREE.Mesh(ringGeometry, ringMaterial)
    pointer.matrixAutoUpdate = false
    pointer.visible = false
    scene.add(pointer)

    //FBX MODEL
    const fbxLoader = new FBXLoader()
    fbxLoader.load(
        'models/DoughNut_FBX.fbx',
        (object) => {
            preloaded = object
            preloaded.scale.multiplyScalar(0.005); 
        },
        (xhr) => {
            console.log((xhr.loaded / xhr.total) * 100 + '% loaded')
        },
        (error) => {
            console.log(error)
        }
    )

    // create GEOMETRY 

    // LIGHTS 
    const dirlight = new THREE.DirectionalLight(0xffffff, 1)
    dirlight.target.position.set(-1, 0, 0)
    scene.add(dirlight)
    scene.add(dirlight.target)

    // AR BUTTON
    const button = ARButton.createButton(renderer,{
        requiredFeatures: ["hit-test"] // hit test features to place thing on surfaces
    })
    document.body.appendChild(button)

}

function animate(){
    renderer.setAnimationLoop(render)
}

function render (timestamp, frame) {
    // rotateModel()
    if(frame){
        if(!hitTestSourceAvailable){
            initHitSource()
        }
        if(hitTestSourceAvailable){
            const hitTestResult = frame.getHitTestResults(hitTestSource)

            if(hitTestResult.length > 0){
                const hitPoint = hitTestResult[0]
                const pose = hitPoint.getPose(localSpace)
                pointer.matrix.fromArray(pose.transform.matrix)
                pointer.visible = true
            }
            else {
                pointer.visible = false
            }
        }
    }
    renderer.render(scene, camera)
}

function rotateModel() {
   
}  

function onSelect(){
    console.log("Touch tunnistettu")
    if(pointer.visible){
        let model = preloaded.clone()
        scene.add(model)
        model.position.setFromMatrixPosition(pointer.matrix)
        model.quaternion.setFromRotationMatrix(pointer.matrix)   
    }
}

async function initHitSource(){
    const session = renderer.xr.getSession()
    const viewerSpace = await session.requestReferenceSpace("viewer")
    localSpace = await session.requestReferenceSpace("local")
    hitTestSource = await session.requestHitTestSource({space: viewerSpace})
    hitTestSourceAvailable = true
    
    session.addEventListener("end", () => {
        hitTestSourceAvailable = false
        hitTestSource = null
    })
}