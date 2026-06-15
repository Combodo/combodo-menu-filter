def infra

node(){
    properties([
            buildDiscarder(
                    logRotator(
                            numToKeepStr: "100")
            )
    ])

  checkout scm

  infra = load '/var/lib/jenkins/workspace/itop-test-infra_9539-behat/src/Infra.groovy'
}

infra.call()