name := "AntidotSearchPlay"

version := "1.0-SNAPSHOT"

libraryDependencies ++= Seq(
  javaJdbc,
  javaEbean,
  cache
)     

play.Project.playJavaSettings

libraryDependencies += "com.google.protobuf" % "protobuf-java" % "2.4.1"

libraryDependencies += "commons-validator" % "commons-validator" % "1.4.0"

libraryDependencies += "org.apache.httpcomponents" % "httpclient" % "4.3"

libraryDependencies += "net.antidot" % "protobuf" % "7.6-SNAPSHOT"

resolvers += "Antidot" at "http://deliver-java/archiva/browse"